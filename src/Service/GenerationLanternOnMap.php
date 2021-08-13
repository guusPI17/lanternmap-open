<?php

namespace App\Service;

use App\DTO\Feature\FeatureCollectionDTO;
use App\DTO\Feature\FeatureDTO;
use App\DTO\Feature\FeatureRatingDTO;
use App\DTO\Feature\GeometryDTO;
use App\DTO\Feature\PropertiesDTO;
use App\DTO\Feature\RatingCollectionDTO;

class GenerationLanternOnMap
{
    public const LENGTH_FROM_STREET = 2;
    private $calculationCoordinatesLights;
    private $defaultData;
    private $newData;

    public function __construct(CalculationCoordinatesLights $calculationCoordinatesLights)
    {
        $this->calculationCoordinatesLights = $calculationCoordinatesLights;
    }

    /**
     * Расстояние между географическими координатами.
     */
    private function distanceInKmBetweenEarthCoordinates(float $lon1, float $lat1, float $lon2, float $lat2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) + sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * Генерация координат осветительных приборов.
     */
    private function generationOfCoordinatesOfLights(FeatureRatingDTO $featureRating, int $startIndex): array
    {
        $lineString = $featureRating->getFeature()->getGeometry()->getCoordinates();
        // расстояние от линии до расстановки фонаря(радиус)
        $r = $featureRating->getFeature()->getProperties()->getWidth() + self::LENGTH_FROM_STREET;
        $pitchLamp = $featureRating->getOptimalStepLantern(); // шаг расстановки фонарей для данной улицы
        $features = [];
        $indexLantern = 0;
        for ($i = 0; $i < count($lineString) - 1; ++$i) {
            $x1 = $lineString[$i][0];
            $y1 = $lineString[$i][1];
            $x2 = $lineString[$i + 1][0];
            $y2 = $lineString[$i + 1][1];
            $coordsLine = [
                [$x1, $y1],
                [$x2, $y2],
            ];

            $lengthLine = $this->distanceInKmBetweenEarthCoordinates($x1, $y1, $x2, $y2) * 1000; // длина отрезка улицы
            $countLamp = $lengthLine / $pitchLamp;
            //var_dump($countLamp);

            $numberStrLine = 'medium';
//            if (0 === $i) {
//                $numberStrLine = 'first';
//            }
//            if (count($lineString) - 1 === $i + 1) {
//                $numberStrLine = 'last';
//            }
            $arrCoords = $this->calculationCoordinatesLights->getXYLightsOfLine($coordsLine, $countLamp, $r, $numberStrLine);
            foreach ($arrCoords as $jKey => $jValue) {
                $newFeature = new FeatureDTO();
                $newFeature->setType('Feature');
                $newFeature->setGeometry((new GeometryDTO([$jValue[0], $jValue[1]])));
                $properties = new PropertiesDTO();
                $properties->setPrice($featureRating->getLanterns()[$indexLantern]->getPrice());
                $properties->setNameLantern($featureRating->getLanterns()[$indexLantern]->getName());
                $properties->setHeight($featureRating->getOptimalHeightLantern());
                $newFeature->setProperties($properties);
                $features[$startIndex] = $newFeature;
                ++$startIndex;
                ++$indexLantern;
            }
        }

        return $features;
    }

    /**
     * Получить коллекцию фактур с новыми сгенерированными точками (фонарями).
     */
    public function getFeatureCollection(RatingCollectionDTO $defaultData): FeatureCollectionDTO
    {
        $this->defaultData = $defaultData;

        $lastIndex = count($this->defaultData->getFeatures());
        $mergeFeatures = [];
        /** @var FeatureRatingDTO $featureRating */
        foreach ($this->defaultData->getFeatures() as $featureRating) {
            $mergeFeatures[] = $featureRating->getFeature();
        }
        /** @var FeatureRatingDTO $featureRating */
        foreach ($this->defaultData->getFeatures() as $i => $featureRating) {
            //echo "<pre>";
            //var_dump($featureRating->getLanterns());
            //echo "<pre>";
            if ($featureRating->getUse()) {
                $featuresPoint = $this->generationOfCoordinatesOfLights($featureRating, $lastIndex);
                $mergeFeatures = array_merge($featuresPoint, $mergeFeatures);
                $lastIndex += count($featuresPoint);
            }
        }
        //exit;
        $this->newData = new FeatureCollectionDTO();
        $this->newData->setType('FeatureCollection');
        $this->newData->setFeatures($mergeFeatures);

        return $this->newData;
    }
}
