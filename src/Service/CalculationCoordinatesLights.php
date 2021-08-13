<?php

namespace App\Service;

class CalculationCoordinatesLights
{
    private const ONE_METER_X = 0.00001498300056972186;
    private const ONE_METER_Y = 0.00000998300056972186;

    /**
     * Возвращает градус поворота.
     */
    private function getDegrees(float $x1, float $y1, float $x2, float $y2): float
    {
        if ($x1 == $x2 && $y1 == $y2) {
            //var_dump('совпадают координаты');
            //exit;
        }
        $deltaX = abs($x1 - $x2);
        $deltaY = abs($y1 - $y2);

        // если линия вертикальная
        if (0 == $deltaX) {
            if ($y2 >= $y1) {
                return 90;
            }

            return 270;
        }
        // если линия горизонтальна
        if (0 == $deltaY) {
            if ($x2 > $x1) {
                return 0;
            }

            return 180;
        }
        $gipotenuza = sqrt($deltaX ** 2 + $deltaY ** 2);
        $sinF = $deltaY / $gipotenuza;
        $arcSin = rad2deg(asin($sinF));

        // угол между двумя точками
        if ($x2 > $x1) {
            if ($y2 > $y1) {
                return $arcSin;
            }

            return 360 - $arcSin;
        } else {
            if ($y2 > $y1) {
                return 180 - $arcSin;
            }

            return 180 + $arcSin;
        }
    }

    /**
     * Функция смещает переданные координаты на указанный радиус в пределах окружности
     * и перемещает точку по окружности на указанное число градусов.
     *
     * @param float $rx      - радиус
     * @param float $ry      - радиус
     * @param float $degrees - градусы
     */
    private function moveAndRouteDot(float $x, float $y, float $rx, float $ry, float $degrees): array
    {
        $newX = $x;
        $newY = $y;

        if (0 == $degrees) {
            return [$x + $rx, $y];
        }
        if (90 == $degrees) {
            return [$x, $y + $ry];
        }
        if (180 == $degrees) {
            return [$x - $rx, $y];
        }
        if (270 == $degrees) {
            return [$x, $y - $ry];
        }
        $newDegrees = $degrees;

        if ($newDegrees > 180) {
            $newDegrees -= 180;
        }
        if ($newDegrees > 90) {
            $newDegrees -= 180;
            $newDegrees *= -1;
        }

        $sinF = sin(deg2rad($newDegrees));
        $cosF = cos(deg2rad($newDegrees));
        $h = $ry * $sinF;
        $l = $rx * $cosF;
        if ($degrees > 0 && $degrees < 180) {
            $newY += $h;
        } else {
            $newY -= $h;
        }
        if ($degrees > 90 && $degrees < 270) {
            $newX -= $l;
        } else {
            $newX += $l;
        }

        return [$newX, $newY];
    }

    /**
     * Получить координаты осветительных приборов для всего отрезка улицы
     * Фонари расставляются равномерно.
     *
     * @param int   $countLamp - количество фонарей
     * @param float $r         - радиус от улицы
     * @param bool  $lastLine  - последний отрезок улицы
     */
    public function getXYLightsOfLine(
        array $coordsLine,
        int $countLamp,
        float $r,
        string $numberStrLine
    ): array {
        $degrees = $this->getDegrees(
            $coordsLine[0][0],
            $coordsLine[0][1],
            $coordsLine[1][0],
            $coordsLine[1][1]);
        $degrees += 270;
        if ($degrees > 360) {
            $degrees -= 360;
        }

        if (0 >= $countLamp - 1) {
            return [];
        }
        $deltaX = ($coordsLine[1][0] - $coordsLine[0][0]) / ($countLamp - 1);
        $deltaY = ($coordsLine[1][1] - $coordsLine[0][1]) / ($countLamp - 1);

        $arrCoords = [];
        for ($i = 0; $i < $countLamp; ++$i) {
//            if ('last' !== $numberStrLine) {
//                if ($i + 1 == $countLamp) {
//                    break;
//                }
//            }
//            if ('first' !== $numberStrLine) {
//                if (0 == $i) {
//                    // нужно сместить
//                }
//            }
            $newX = $coordsLine[0][0] + $deltaX * $i;
            $newY = $coordsLine[0][1] + $deltaY * $i;
            $newXY = $this->moveAndRouteDot(
                $newX,
                $newY,
                self::ONE_METER_X * $r,
                self::ONE_METER_Y * $r,
                $degrees
            );
            $arrCoords[] = $newXY;
        }

        return $arrCoords;
    }
}
