<?php

namespace App\Service;

use App\DTO\Feature\FeatureDTO;
use App\DTO\Feature\FeatureRatingDTO;
use App\DTO\Feature\GeometryDTO;

class CalculationGeometryWithLines
{
    /**
     * Расстояние от точки до прямой.
     */
    private function distanceFromPointToLine(float $xPoint, float $yPoint, float $x1, float $y1, float $x2, float $y2
    ): float {
        $temp1 = abs(($y2 - $y1) * $xPoint - ($x2 - $x1) * $yPoint + $x2 * $y1 - $y2 * $x1);
        $temp2 = sqrt((($y2 - $y1) ** 2.0) + (($x2 - $x1) ** 2.0));
        if(0 == $temp2){
            $temp2 = 0.00072881469966474;
        }
        return  $temp1 / $temp2;
    }

    /**
     * Возвращает ближайшую линию к заданным координатам.
     *
     * @param array $features Массив фактур (линий)
     * @param array $coords   Заданные координаты ([longitude,latitude])
     *
     * @throws \Exception
     */
    public function getClosestLineToGivenCoords(array $features, array $coords): FeatureDTO
    {
        // getFeatureStreet
        $foundFeature = null;
        $minDistance = PHP_FLOAT_MAX;
        /** @var FeatureDTO[] $features */
        foreach ($features as $feature) {
            /** @var GeometryDTO $geometry */
            $coordsStreet = $feature->getGeometry()->getCoordinates();
            for ($i = 0; $i < count($coordsStreet) - 1; ++$i) {
                $x1 = $coordsStreet[$i][0];
                $y1 = $coordsStreet[$i][1];
                $x2 = $coordsStreet[$i + 1][0];
                $y2 = $coordsStreet[$i + 1][1];
                $distance = $this->distanceFromPointToLine($coords[0], $coords[1], $x1, $y1, $x2, $y2);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $foundFeature = $feature;
                }
            }
        }

        return $foundFeature;
    }

    /**
     * Возвращает фактуру с минимальной дистанцией до заданной фактуры среди всех элементов массива.
     *
     * @param array $features
     */
    public function getFeaturePriority(array $featuresRating, float $rating, array $arrayWithSamePriority): ?object
    {
        /** @var FeatureRatingDTO $foundFeature */
        $foundFeature = null;
        $minDistance = PHP_FLOAT_MAX;
        /** @var FeatureRatingDTO $feature */
        foreach ($featuresRating as $feature) {
            if ($feature->getRating() > $rating) {
                $response = $this->getFeaturePriorityAmongOneItem($arrayWithSamePriority, $feature);
                if ($response['minDistance'] < $minDistance) {
                    $minDistance = $response['minDistance'];
                    $foundFeature = $response['foundFeature'];
                }
            }
        }

        return is_null($foundFeature) ? null : $foundFeature->getFeature();
    }

    /**
     * Возвращает фактуру с минимальной дистанцией до заданной фактуры среди одого элемента массива.
     *
     * @param array $features
     */
    private function getFeaturePriorityAmongOneItem(array $featuresPriority, FeatureRatingDTO $mainFeature): array
    {
        $coords1 = $mainFeature->getFeature()->getGeometry()->getCoordinates();
        $foundFeature = null;
        $minDistance = PHP_FLOAT_MAX;
        /* @var FeatureRatingDTO $feature */
        foreach ($featuresPriority as $feature) {
            $coords2 = $feature->getFeature()->getGeometry()->getCoordinates();
            $distance = $this->getMinDistanceBetweenLines($coords1, $coords2);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $foundFeature = $feature;
            }
        }

        return [
            'foundFeature' => $foundFeature,
            'minDistance' => $minDistance,
        ];
    }

    private function ras(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3): float
    {
        if ($x1 === $x2) { // Если отрезок вертикальный - меняем местами координаты каждой точки.
            $this->swap($x1, $y1);
            $this->swap($x2, $y2);
            $this->swap($x3, $y3);
        }
        $k = ($y1 - $y2) / ($x1 - $x2); // Ищем коэффициенты уравнения прямой, которому принадлежит данный отрезок.
        $d = $y1 - $k * $x1;
        $xz = ($x3 * $x2 - $x3 * $x1 + $y2 * $y3 - $y1 * $y3 + $y1 * $d - $y2 * $d) / ($k * $y2 - $k * $y1 + $x2 - $x1);
        $dl = -1;
        if (($xz <= $x2 && $xz >= $x1) || ($xz <= $x1 && $xz >= $x2)) {
            $dl = sqrt(($x3 - $xz) * ($x3 - $xz) +
                ($y3 - $xz * $k - $d) * ($y3 - $xz * $k - $d)); // Проверим лежит ли основание высоты на отрезке.
        }

        return $dl;
    }

    /**
     * Меняет местами элементы.
     */
    private function swap(&$x, &$y): void
    {
        $tmp = $x;
        $x = $y;
        $y = $tmp;
    }

    /**
     * Возвращает минимальную дистанцию между двумя отрезками.
     */
    private function getMinDistanceBetweenLines(array $coords1, array $coords2): float
    {
        $min = -1;
        $t = -2;
        $s = -2;
        $o = ($coords1[1][0] - $coords1[0][0]) * (-$coords2[1][1] + $coords2[0][1]) -
            ($coords1[1][1] - $coords1[0][1]) * (-$coords2[1][0] + $coords2[0][0]);
        $o1 = ($coords1[1][0] - $coords1[0][0]) * ($coords2[0][1] - $coords1[0][1]) -
            ($coords1[1][1] - $coords1[0][1]) * ($coords2[0][0] - $coords1[0][0]);
        $o2 = (-$coords2[1][1] + $coords2[0][1]) * ($coords2[0][0] - $coords1[0][0]) -
            (-$coords2[1][0] + $coords2[0][0]) * ($coords2[0][1] - $coords1[0][1]);
        if (0 != $o) {
            $t = $o1 / $o;
            $s = $o2 / $o;
        }
        if (($t >= 0 && $s >= 0) && ($t <= 1 && $s <= 1)) {
            $min = 0;
        }// Проверим пересекаются ли отрезки.
        else {
            // Найдём наименьшую высоту опущенную из конца одного отрезка на другой.
            $dl1 = $this->ras($coords1[0][0], $coords1[0][1], $coords1[1][0], $coords1[1][1], $coords2[0][0],
                $coords2[0][1]);
            $min = $dl1;
            $dl2 = $this->ras($coords1[0][0], $coords1[0][1], $coords1[1][0], $coords1[1][1], $coords2[1][0],
                $coords2[1][1]);
            if (($dl2 < $min && -1 != $dl2) || -1 == $min) {
                $min = $dl2;
            }
            $dl3 = $this->ras($coords2[0][0], $coords2[0][1], $coords2[1][0], $coords2[1][1], $coords1[0][0],
                $coords1[0][1]);
            if (($dl3 < $min && -1 != $dl3) || -1 == $min) {
                $min = $dl3;
            }
            $dl4 = $this->ras($coords2[0][0], $coords2[0][1], $coords2[1][0], $coords2[1][1], $coords1[1][0],
                $coords1[1][1]);
            if (($dl4 < $min && -1 != $dl4) || -1 == $min) {
                $min = $dl4;
            }
            if (-1 == $min) {
                // В случае, если невозможно опустить высоту найдём минимальное расстояние между точками.
                $dl1 = sqrt(($coords1[0][0] - $coords2[0][0]) * ($coords1[0][0] - $coords2[0][0]) +
                    ($coords1[0][1] - $coords2[0][1]) * ($coords1[0][1] - $coords2[0][1]));
                $min = $dl1;
                $dl2 = sqrt(($coords1[1][0] - $coords2[1][0]) * ($coords1[1][0] - $coords2[1][0]) +
                    ($coords1[1][1] - $coords2[1][1]) * ($coords1[1][1] - $coords2[1][1]));
                if ($dl2 < $min) {
                    $min = $dl2;
                }
                $dl3 = sqrt(($coords1[1][0] - $coords2[0][0]) * ($coords1[1][0] - $coords2[0][0]) +
                    ($coords1[1][1] - $coords2[0][1]) * ($coords1[1][1] - $coords2[0][1]));
                if ($dl3 < $min) {
                    $min = $dl3;
                }
                $dl4 = sqrt(($coords1[0][0] - $coords2[1][0]) * ($coords1[0][0] - $coords2[1][0]) +
                    ($coords1[0][1] - $coords2[1][1]) * ($coords1[0][1] - $coords2[1][1]));
                if ($dl4 < $min) {
                    $min = $dl4;
                }
            }
        }

        return $min;
    }
}
