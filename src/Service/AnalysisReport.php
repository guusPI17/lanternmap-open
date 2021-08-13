<?php

namespace App\Service;

use App\DTO\Feature\FeatureRatingDTO;
use App\DTO\Feature\RatingCollectionDTO;
use App\Entity\Map;
use App\Entity\StreetClass;
use Doctrine\ORM\EntityManager;
use PHPExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;

class AnalysisReport
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createTotalReport(RatingCollectionDTO $ratingCollectionDTO, Map $map, float $budget): PHPExcel
    {
        $document = new PHPExcel();

        $columns = [
            '№',
            'Выбираем?',
            'Название улицы',
            'Класс объекта',
            'Количество людей',
            'Рейтинг',
            '*Приоритет',
            'Название фонаря',
            'Количество фонарей',
            'Высота установки',
            'Расстояние между фонарями',
            'Стоимость одного фонаря',
            'Общая стоимость всех фонарей',
        ];

        try {
            $sheet = $document->setActiveSheetIndex(0); // Выбираем первый лист в документе

            $x = 0; // Начальная координата x
            $y = 3; // Начальная координата y

            $sheet->setCellValueByColumnAndRow($x, $y, 'Название карты');
            $sheet->getStyleByColumnAndRow($x, $y)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $document->getActiveSheet()->mergeCellsByColumnAndRow($x, $y, $x + count($columns) - 1, $y);
            ++$y;

            $sheet->setCellValueByColumnAndRow($x, $y, $map->getName());
            $sheet->getStyleByColumnAndRow($x, $y)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $document->getActiveSheet()->mergeCellsByColumnAndRow($x, $y, $x + count($columns) - 1, $y);

            ++$y;

            $varX = $x;
            foreach ($columns as $column) {
                // Красим ячейку
                $sheet->getStyleByColumnAndRow($varX, $y)
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('4dbf62');

                $sheet->setCellValueByColumnAndRow($varX++, $y, $column);
            }
            /** @var FeatureRatingDTO $item */
            foreach ($ratingCollectionDTO->getFeatures() as $key => $item) {
                $varX = $x;
                ++$y;

                // №
                $sheet->setCellValueByColumnAndRow($varX++, $y, $key + 1);

                // Выбираем?
                $value = $item->getUse();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value ? 'Да' : 'Нет');

                // Название улицы
                $value = $item->getFeature()->getProperties()->getNameStreet();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Класс объекта
                $value = $item->getFeature()->getProperties()->getClassObject();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Количество людей
                $averageIllumination = $this->em->getRepository(StreetClass::class)
                    ->findOneBy(['name' => $item->getFeature()->getProperties()->getClassObject()])->getAverageIllumination();
                $value = $item->getRating() / ($averageIllumination * (10 ** -1));
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Рейтинг
                $value = $item->getRating();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // *Приоритет
                $value = $item->getFeature()->getProperties()->getPriority();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Название фонаря
                $value = $item->getLanterns()[0]->getName();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Количество фонарей
                $value = count($item->getLanterns());
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Высота установки
                $value = $item->getOptimalHeightLantern();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Расстояние между фонарями
                $value = $item->getOptimalStepLantern();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Стоимость одного фонаря
                $value = $item->getLanterns()[0]->getPrice();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);

                // Общая стоимость всех фонарей
                $value = $item->getOptimalTotalCost();
                $sheet->setCellValueByColumnAndRow($varX++, $y, $value);
            }
            ++$y;

            $sumCost = 0;
            $sumRating = 0;
            foreach ($ratingCollectionDTO->getFeatures() as $item) {
                if ($item->getUse()) {
                    $sumCost += $item->getOptimalTotalCost();
                    $sumRating += $item->getRating();
                }
            }
            $value = "Суммарный рейтинг выбранных улиц: $sumRating";
            $sheet->setCellValueByColumnAndRow($x, $y++, $value);

            $value = "Весь бюджет: $budget";
            $sheet->setCellValueByColumnAndRow($x, $y++, $value);

            $value = "Всего потрачено на фонари: $sumCost";
            $sheet->setCellValueByColumnAndRow($x, $y++, $value);

            $value = 'Осталось после покупки:' . ($budget - $sumCost);
            $sheet->setCellValueByColumnAndRow($x, $y++, $value);

            $value = '*Приоритет = Данный параметр используется лишь тогда, когда у улиц одинаковый рейтинг.';
            $sheet->setCellValueByColumnAndRow($x, $y++, $value);
        } catch (\PHPExcel_Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $document;
    }
}
