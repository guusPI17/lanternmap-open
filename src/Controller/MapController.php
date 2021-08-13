<?php

namespace App\Controller;

use App\DTO\BadResponseDTO;
use App\DTO\Feature\FeatureCollectionDTO;
use App\DTO\Feature\FeatureRatingDTO;
use App\DTO\Feature\RatingCollectionDTO;
use App\DTO\People\PeopleCollectionDTO;
use App\Entity\Map;
use App\Entity\StreetClass;
use App\Service\AnalysisReport;
use App\Service\CalculationStreetLights;
use App\Service\CalculationStreetsByParameters;
use App\Service\CleaningData;
use App\Service\DataAveraging;
use App\Service\DeterminationOffsetPeoples;
use App\Service\GenerationLanternOnMap;
use App\Service\StreetRatingCalculation;
use JMS\Serializer\SerializerBuilder;
use PHPExcel_Writer_Excel2007;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/menu/map")
 */
class MapController extends AbstractController
{
    /**
     * @Route("/{id}", name="map_show", methods={"GET", "POST"})
     */
    public function show(Map $map): Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render(
            'map/show.html.twig',
            [
                'map' => $map,
                'user' => $this->getUser(),
                'streetClass' => $em->getRepository(StreetClass::class)->findAll(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="map_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Map $map): Response
    {
        if ($this->isCsrfTokenValid('delete' . $map->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($map);
            $entityManager->flush();
        }

        return $this->redirectToRoute('menu_show');
    }

    /**
     * @Route("/{id}/loadmap", name="map_load", methods={"GET"})
     */
    public function load(Map $map): Response
    {
        $response = new Response($map->getData());
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/{id}/savemap", name="map_save", methods={"POST"})
     */
    public function save(Request $request, Map $map): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $map->setData($request->request->get('dataMap'));
        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}/downloadreport", name="map_downloadReport", methods={"GET"})
     *
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function downloadReport(Request $request, Map $map): Response
    {
        if (is_null($map->getReport())) {
            throw $this->createNotFoundException('Отчет отсутствует.');
        }
        $file = $this->getParameter('report_directory') . '/' . $map->getReport();
        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $map->getReport()
        );

        return $response;
    }

    /**
     * @Route("/{id}/analysis", name="map_analysis", methods={"GET"})
     *
     * @throws \Exception
     */
    public function analysis(
        Request $request,
        Map $map,
        GenerationLanternOnMap $generationLanternOnMap,
        CleaningData $cleaningData,
        DataAveraging $dataAveraging,
        StreetRatingCalculation $streetRatingCalculation,
        DeterminationOffsetPeoples $determinationOffsetPeoples,
        CalculationStreetsByParameters $calculationStreetsByParameters,
        CalculationStreetLights $calculationStreetLights,
        AnalysisReport $analysisReport,
        Filesystem $filesystem
    ): Response {
        $budget = $request->query->get('budget');

        $em = $this->getDoctrine()->getManager();
        $serializer = SerializerBuilder::create()->build();
        $details = []; // массив подробностей во время ошибки
        try {
            if (empty($map->getLanterns()->getValues())) {
                $text = 'Фонари отсутствуют. Загрузите фонари';
                throw new \RuntimeException($text, 400);
            }
            // получение данных о фактурах из БД
            $featureCollectionDTO = $serializer->deserialize($map->getData(), FeatureCollectionDTO::class, 'json');

            // очистка фактур от лишних объектов на карте(точки)
            $featureCollectionDTO = $cleaningData->cleaningFeaturesOfUselessObjects($featureCollectionDTO);

            if (empty($featureCollectionDTO->getFeatures())) {
                $text = 'Улиц не обнаружено. Добавьте улицы.';
                throw new \RuntimeException($text, 400);
            }

            // получение данных о передвижении людей из файла
            $nameDataMovementFile = $em->getRepository(Map::class)
                ->findOneBy(['id' => $map->getId()])
                ->getLocality()
                ->getDataMovement();
            $path = $this->getParameter('data_movement_directory') . '/' . $nameDataMovementFile;
            try {
                $jsonPeoples = stream_get_contents(fopen($path, 'rb'));
                if (empty($jsonPeoples)) {
                    $text = 'Геоданные не обнаружены. Загрузите или сгенерируйте их.';
                    throw new \RuntimeException($text, 400);
                }
            } catch (\Exception $e) {
                $text = 'Геоданные не обнаружены. Загрузите или сгенерируйте их.';
                throw new \RuntimeException($text, 400);
            }
            $peopleCollectionDTO = $serializer->deserialize($jsonPeoples, PeopleCollectionDTO::class, 'json');

            // очистка данных передвижения от лишних локаций передвижения по одной улице
            $peopleCollectionDTO = $cleaningData->cleaningPeoplesOfUselessLocations(
                $peopleCollectionDTO,
                $featureCollectionDTO
            );

            // усреднение данных по часу, дню и месяцу
            $peopleCollectionDTO = $dataAveraging->averagingOneDate($peopleCollectionDTO, 'H');
            $peopleCollectionDTO = $dataAveraging->averagingOneDate($peopleCollectionDTO, 'd');
            $peopleCollectionDTO = $dataAveraging->averagingOneDate($peopleCollectionDTO, 'm');

            // получение рейтинга улиц
            $ratingCollectionDTO = $streetRatingCalculation->calculation($peopleCollectionDTO, $featureCollectionDTO);

            // вызов сервиса по определению смещения потоков людей
            $errorsRating = $determinationOffsetPeoples->getErrorsRating($ratingCollectionDTO);

            if (!empty($errorsRating)) {
                $textError =
                    'Рейтинг следующих улиц после вычисления был приблизительно равный. Измените приоритет в свойствах улицы.';
                $details = $errorsRating;

                throw new \RuntimeException($textError, 400);
            }
            // определения фонарей для улиц
            $ratingCollectionDTO = $calculationStreetLights->calculation($ratingCollectionDTO);

            // опеределение списка улиц по параметру
            $ratingCollectionDTO = $calculationStreetsByParameters->calculation($ratingCollectionDTO, $budget);

            if (is_null($ratingCollectionDTO)) {
                $details = [
                    'Маленький бюджет',
                ];
                throw new \Exception('Не удалось рассчитать месторасположения фонарей ни для одной улицы.', 400);
            }

            // получение списка фактур с новыми сгенерированными точками (фонарями)
            $featureCollectionDTO = $generationLanternOnMap->getFeatureCollection($ratingCollectionDTO);
            $jsonFeatures = $serializer->serialize($featureCollectionDTO, 'json');

            // генерация названия файла и папки для отчета
            $nameFile = $this->getUser()->getEmail() . '-' .
                $map->getName() . '-' . (new \DateTime())->format('Y-m-d H:i:s') . '.xls';
            $filePath = $this->getParameter('report_directory');
            if (!file_exists($filePath)) {
                $filesystem->mkdir($filePath);
            }

            // сортировка массива для отчета
            $features = $ratingCollectionDTO->getFeatures();
            usort($features, static function ($object1, $object2) {
                return $object1->getUse() < $object2->getUse();
            });
            $ratingCollectionDTO->setFeatures($features);

            // создание отчета
            $xls = $analysisReport->createTotalReport($ratingCollectionDTO, $map, $budget);
            $objWriter = new PHPExcel_Writer_Excel2007($xls);
            $objWriter->save($filePath . '/' . $nameFile);
            $map->setReport($nameFile);

            // сохраняем полученные данные в БД
            $map->setData($jsonFeatures);
            $em->flush();
        } catch (\Exception $e) {
            $badResponseDTO = new BadResponseDTO($e->getCode(), $e->getMessage());
            $badResponseDTO->setDetails($details);

            return new JsonResponse(
                $serializer->serialize($badResponseDTO, 'json'),
                $e->getCode(),
                [],
                true
            );
        }

        return new JsonResponse(
            $jsonFeatures,
            200,
            [],
            true
        );
    }
}
