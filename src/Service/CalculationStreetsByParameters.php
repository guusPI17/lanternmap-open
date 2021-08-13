<?php

namespace App\Service;

use App\DTO\Curve\CurveCoefTableDTO;
use App\DTO\Feature\FeatureDTO;
use App\DTO\Feature\FeatureRatingDTO;
use App\DTO\Feature\PropertiesDTO;
use App\DTO\Feature\RatingCollectionDTO;
use App\DTO\Lantern\LanternDTO;
use App\DTO\Lantern\MinIlluminationDTO;
use App\DTO\LanternType\LanternTypeCoefTableDTO;
use App\DTO\LanternType\TableXhDTO;
use App\DTO\LanternType\TableYhDTO;
use App\Entity\Lantern;
use App\Entity\StreetClass;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\SerializerInterface;

class CalculationStreetsByParameters
{
    private function int32ToIndexes(int $value): array
    {
        $arrayInt = [];
        $tempValue = 1;
        for ($i = 0; $i < 32; ++$i) {
            if (($tempValue & $value) > 0) {
                $arrayInt[] = $i;
            }
            $tempValue *= 2;
        }
        return $arrayInt;
    }

    public function calculation(RatingCollectionDTO $ratingCollectionDTO, float $budget): ?RatingCollectionDTO
    {
        // получение улиц по критерию макс(рейтинг) и суммарный бюджет <= budget
        $featuresRating = $ratingCollectionDTO->getFeatures();
        $arrayIndexes = [];
        for ($i = 0; $i < (2 ** count($featuresRating)); ++$i) {
            $tmpStruct = [
                'intIndexes' => $i,
                'sumCost' => 0,
                'sumRating' => 0,
            ];
            $array = $this->int32ToIndexes($i);
            foreach ($array as $value) {
                $tmpStruct['sumCost'] += $featuresRating[$value]->getOptimalTotalCost();
                $tmpStruct['sumRating'] += $featuresRating[$value]->getRating();
            }
            if ($tmpStruct['sumCost'] <= $budget) {
                $arrayIndexes[] = $tmpStruct;
            }
        }
        $collectionMaxRating = $arrayIndexes[0];
        foreach ($arrayIndexes as $index) {
            if ($collectionMaxRating['sumRating'] < $index['sumRating']) {
                $collectionMaxRating = $index;
            }
        }
        $resultFeatures = [];
        foreach ($this->int32ToIndexes($collectionMaxRating['intIndexes']) as $value) {
            $resultFeatures[] = $featuresRating[$value];
        }
        // если бюджета не хватило на улицы
        if (empty($resultFeatures)) {
            return null;
        }
        // получение улиц которые не вошли в коненчый список
        $resultAddFeatures = [];
        foreach ($featuresRating as $featureRating) {
            $check = false;
            foreach ($resultFeatures as $feature) {
                $nameStreetInResult = $feature->getFeature()->getProperties()->getNameStreet();
                $nameStreetInMain = $featureRating->getFeature()->getProperties()->getNameStreet();
                if ($nameStreetInResult === $nameStreetInMain) {
                    $check = true;
                    break;
                }
            }
            if (!$check) {
                $cloneFeature = clone $featureRating;
                $cloneFeature->setUse(false);
                $resultAddFeatures[] = $cloneFeature;
            }
        }
        $mergeFeatures = array_merge($resultFeatures, $resultAddFeatures);
        $collection = new RatingCollectionDTO();
        $collection->setFeatures($mergeFeatures);

        return $collection;
    }

}
