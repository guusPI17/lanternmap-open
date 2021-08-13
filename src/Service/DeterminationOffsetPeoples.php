<?php

namespace App\Service;

use App\DTO\Feature\FeatureRatingDTO;
use App\DTO\Feature\RatingCollectionDTO;

class DeterminationOffsetPeoples
{
    private $ratingCollectionDTO;

    private $calculationGeometryWithLines;

    public function __construct(CalculationGeometryWithLines $calculationGeometryWithLines)
    {
        $this->calculationGeometryWithLines = $calculationGeometryWithLines;
    }

    /**
     * Получить массив улиц с одинаковыми рейтингами.
     */
    private function getArraysWithSameRating(): array
    {
        $arraysWithSameRating = [];
        $featuresRating = $this->ratingCollectionDTO->getFeatures();
        foreach ($featuresRating as $key => $featureRating) {
            $arraysWithSameRating[$featureRating->getRating()][] = $featureRating;
            unset($featuresRating[$key]);
        }

        // удаляем элементы из массива, у которых один элемент
        foreach ($arraysWithSameRating as $rating => $arrayWithSameRating) {
            if (1 >= count($arrayWithSameRating)) {
                unset($arraysWithSameRating[$rating]);
            }
        }

        return $arraysWithSameRating;
    }

    /**
     * Получить массив улиц с одинаковыми приоритетами на основе одинакового рейтинга.
     */
    private function getArraysWithSamePriority(array $arraysWithSameRating): array
    {
        $arraysWithSamePriority = [];
        foreach ($arraysWithSameRating as $rating => $arrayWithSameRating) {
            /** @var FeatureRatingDTO $value */
            foreach ($arrayWithSameRating as $i => $value) {
                $nameKey = $value->getFeature()->getProperties()->getPriority() . '_' . +$rating;
                $arraysWithSamePriority[$nameKey][] = $value;
                unset($arrayWithSameRating[$i]);
            }
        }

        // удаляем элементы из массива, у которых один элемент
        foreach ($arraysWithSamePriority as $key => $arrayWithSamePriority) {
            if (1 >= count($arrayWithSamePriority)) {
                unset($arraysWithSamePriority[$key]);
            }
        }

        return $arraysWithSamePriority;
    }

    /**
     * Возвращаем ошибки после вычисления рейтинга связанные со смещением потоков людей.
     */
    public function getErrorsRating(RatingCollectionDTO $ratingCollectionDTO): array
    {
        $this->ratingCollectionDTO = $ratingCollectionDTO;

        // получаем массив улиц с одинаковыми рейтингами
        $arraysWithSameRating = $this->getArraysWithSameRating();
        // получаем массив улиц с одинаковыми приоритетами на основе одинакового рейтинга
        $arraysWithSamePriority = $this->getArraysWithSamePriority($arraysWithSameRating);

        $errorsRating = [];
        $index = 0; // $i в foreach не подходит. Нужно начинать массив с 0.
        /** @var FeatureRatingDTO[] $arrayWithSamePriority */
        foreach ($arraysWithSamePriority as $key => $arrayWithSamePriority) {
            // получаем приоритетную улица
            $rating = explode('_', $key)[1];
            $priorityFeature = $this->calculationGeometryWithLines->getFeaturePriority(
                $this->ratingCollectionDTO->getFeatures(),
                $rating,
                $arrayWithSamePriority
            );
            /** @var FeatureRatingDTO $feature */
            // заполняем ответ для пользователя
            foreach ($arrayWithSamePriority as $feature) {
                $message = $feature->getFeature()->getProperties()->getNameStreet() . ', ';
                if (!isset($errorsRating[$index])) {
                    $errorsRating[$index] = $message;
                } else {
                    $errorsRating[$index] .= $message;
                }
            }
            // удаляем запятую с пробелом в конце
            $errorsRating[$index] = substr($errorsRating[$index], 0, -2);
            // пишем приоритетную улицу
            if (isset($priorityFeature)) {
                $errorsRating[$index] .= '<br>Приоритетная улица: ' .
                    $priorityFeature->getProperties()->getNameStreet();
            } else {
                $errorsRating[$index] .= '<br>Приоритетная улица: любая';
            }
            ++$index;
        }

        return $errorsRating;
    }
}
