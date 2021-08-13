<?php

namespace App\Controller;

use App\DTO\Lantern\LanternCollectionDTO;
use App\DTO\People\PeopleCollectionDTO;
use App\Entity\Lantern;
use App\Entity\Locality;
use App\Entity\Map;
use App\Form\LanternFileType;
use App\Form\LocalityGenerationType;
use App\Form\LocalityType;
use App\Form\MapType;
use App\Service\CleaningData;
use App\Service\GenerationDataMovement;
use App\Service\SavingFile;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/menu")
 */
class MenuController extends AbstractController
{
    public const COEF_COORDS = 10 ** 4;

    /**
     * @Route("/", name="menu_show")
     *
     * @throws \Exception
     */
    public function menu(
        Request $request,
        CleaningData $cleaningData,
        SerializerInterface $serializer,
        GenerationDataMovement $generationDataMovement,
        SavingFile $savingFile
    ): Response
    {
        // Форма для загрузки данных по передвижению
        $locality = new Locality();
        $formLocality = $this->createForm(LocalityType::class, $locality);
        $formLocality->handleRequest($request);

        if ($formLocality->isSubmitted() && $formLocality->isValid()) {
            /** @var Locality $editLocality */
            $editLocality = $formLocality->get('editLocality')->getData();

            /** @var UploadedFile $dataMovement */
            $dataMovement = $formLocality->get('fileDataMovement')->getData();
            if ($dataMovement) {
                $peopleCollectionDTO = $serializer->deserialize(
                    $dataMovement->getContent(),
                    PeopleCollectionDTO::class,
                    'json'
                );
                // вызов сервиса по очистке данных от лишнего времени
                $peopleCollectionDTO = $cleaningData->cleaningPeoplesOfUselessTimestamp($peopleCollectionDTO);

                // перевод данных dataMovement в строку
                $content = $serializer->serialize($peopleCollectionDTO, 'json');

                // вызов сервиса по сохранение dataMovement
                $savingFile->setFilePath($this->getParameter('data_movement_directory'));
                $savingFile->save($editLocality->getName(), $content);

                $editLocality->setDataMovement($savingFile->getFullFileName());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editLocality);
            $entityManager->flush();
            $this->addFlash('success', 'Файл успешно загружен.');

            return $this->redirectToRoute('menu_show');
        }

        // Форма для генерации данных
        $errorsLocality = [];
        $formLocalityGeneration = $this->createForm(LocalityGenerationType::class);
        $formLocalityGeneration->handleRequest($request);

        if ($formLocalityGeneration->isSubmitted() && $formLocalityGeneration->isValid()) {
            /** @var Locality $editLocality */
            $editLocality = $formLocalityGeneration->get('editLocality')->getData();

            // ограничения для генерации данных
            $restrictions = [
                'latitude' => [
                    'min' => $editLocality->getLatitude()[0] * self::COEF_COORDS,
                    'max' => $editLocality->getLatitude()[1] * self::COEF_COORDS,
                ], // широта
                'longitude' => [
                    'min' => $editLocality->getLongitude()[0] * self::COEF_COORDS,
                    'max' => $editLocality->getLongitude()[1] * self::COEF_COORDS,
                ], // долгота
                'coef' => self::COEF_COORDS,
                'timestamp' => [
                    'minYear' => 2019, // в годах
                    'minInterval' => 300, // 5 минут в секундах
                ],
            ];
            // вызов сервиса по геннерации данных передвижения
            $peopleCollectionDTO = $generationDataMovement->generation($restrictions);

            // вызов сервиса по очистке данных от лишнего времени
            $peopleCollectionDTO = $cleaningData->cleaningPeoplesOfUselessTimestamp($peopleCollectionDTO);

            // перевод данных в строку
            $content = $serializer->serialize($peopleCollectionDTO, 'json');

            // вызов сервиса по сохранение dataMovement
            $savingFile->setFilePath($this->getParameter('data_movement_directory'));
            $savingFile->save($editLocality->getName(), $content);

            // перевод данных dataMovement в строку в формате geoJson (необязательно)
            /*$content = $serializer->serialize(
                $generationDataMovement->convertPeoplesToGeoJson($content),
                'json'
            );*/

            // вызов сервиса по сохранение dataMovement в формате geoJson (необязательно)
            $savingFile->setFilePath($this->getParameter('geo_json_generation_directory'));
            $savingFile->save($editLocality->getName(), $content);

            $editLocality->setDataMovement($savingFile->getFullFileName());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editLocality);
            $entityManager->flush();
            $this->addFlash('success', 'Тестовые данные передвижения сгенерированы.');

            return $this->redirectToRoute('menu_show');
        }

        // Форма по загрузке фонарей
        $errorsLanternFile = [];
        $formLanternFile = $this->createForm(LanternFileType::class);
        $formLanternFile->handleRequest($request);

        if ($formLanternFile->isSubmitted() && $formLanternFile->isValid()) {
            /** @var UploadedFile $dataMovement */
            $dataMovement = $formLanternFile->get('lanternFile')->getData();
            if ($dataMovement) {
                $serializer = SerializerBuilder::create()->build();

                $lanternCollectionDTO = $serializer->deserialize(
                    $dataMovement->getContent(),
                    LanternCollectionDTO::class, 'json'
                );

                $entityManager = $this->getDoctrine()->getManager();
                // если фонари есть, то мы их удаляем (старые записи)
                $lanterns = $entityManager->getRepository(Lantern::class)->findAll();
                if (!empty($lanterns)) {
                    foreach ($lanterns as $lantern) {
                        $entityManager->remove($lantern);
                    }
                }
                // сохраняем в БД загруженные фонари
                foreach ($lanternCollectionDTO->getLanterns() as $lanternDTO) {
                    $entityManager->persist(Lantern::fromDTO($lanternDTO, $entityManager));
                }
                $entityManager->flush();

                // добавление всех фонарей ко всем картам
                $maps = $entityManager->getRepository(Map::class)->findAll();
                $lanterns = $entityManager->getRepository(Lantern::class)->findAll();
                foreach ($maps as $map) {
                    foreach ($lanterns as $lantern) {
                        $map->addLantern($lantern);
                    }
                }
                $entityManager->flush();
            }
            $this->addFlash('success', 'Файл успешно загружен.');

            return $this->redirectToRoute('menu_show');
        }

        // Форма по созданию новой карты
        $errorsMap = [];
        $map = new Map();
        $formMap = $this->createForm(MapType::class, $map);
        $formMap->handleRequest($request);

        if ($formMap->isSubmitted() && $formMap->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $checkMap = $entityManager->getRepository(Map::class)->findBy(
                [
                    'userAccount' => $this->getUser(),
                    'name' => $formMap->get('name')->getData(),
                ]
            );
            if (empty($checkMap)) {
                $map->setUserAccount($this->getUser());
                $map->setData('{"type":"FeatureCollection","features":[]}');
                $entityManager->persist($map);

                // добавление всех фонарей к созданной карте
                $lanterns = $entityManager->getRepository(Lantern::class)->findAll();
                foreach ($lanterns as $lantern) {
                    $map->addLantern($lantern);
                }

                $entityManager->flush();

                return $this->redirectToRoute('menu_show');
            }
            $errorsMap[] = 'Данное имя уже занято.';
        }
        // ошибки формы создания карты
        foreach ($formMap->getErrors(true) as $key => $error) {
            $errorsMap[] = $error->getMessage();
        }

        // ошибки формы загрузки передвижения людей
        foreach ($formLocality->getErrors(true) as $key => $error) {
            $errorsLocality[] = $error->getMessage();
        }

        // ошибки формы по загрузке фонарей
        foreach ($formLanternFile->getErrors(true) as $key => $error) {
            $errorsLanternFile[] = $error->getMessage();
        }

        return $this->render(
            'project/menu/show.html.twig',
            [
                'formLocalityGeneration' => $formLocalityGeneration->createView(),
                'formLanternFile' => $formLanternFile->createView(),
                'formMap' => $formMap->createView(),
                'map' => $map,
                'formLocality' => $formLocality->createView(),
                'locality' => $locality,
                'user' => $this->getUser(),
                'maps' => $this->getDoctrine()
                    ->getRepository(Map::class)->findBy(
                        ['userAccount' => $this->getUser()],
                        ['data' => 'ASC']
                    ),
                'errorsMap' => $errorsMap,
                'errorsLocality' => $errorsLocality,
                'errorsLanternFile' => $errorsLanternFile,
            ]
        );
    }
}
