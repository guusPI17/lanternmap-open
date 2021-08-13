<?php

namespace App\Service;

use App\DTO\Feature\FeatureCollectionDTO;
use App\DTO\People\LocationDTO;
use App\DTO\People\PeopleCollectionDTO;
use App\DTO\People\PeopleDTO;

class CleaningData
{
    private $calculationGeometryWithLines;

    public function __construct(CalculationGeometryWithLines $calculationGeometryWithLines)
    {
        $this->calculationGeometryWithLines = $calculationGeometryWithLines;
    }

    /**
     * Очистка от бесполезных фактур(всё кроме линий).
     */
    public function cleaningFeaturesOfUselessObjects(FeatureCollectionDTO $data): FeatureCollectionDTO
    {
        $oldFeatures = $data->getFeatures();
        $newFeatures = [];
        foreach ($oldFeatures as $feature) {
            if ('LineString' === $feature->getGeometry()->getType()) {
                $newFeatures[] = $feature;
            }
        }
        $data->setFeatures($newFeatures);

        return $data;
    }

    /**
     * Очистка от бесполезных данных передвижения людей связанных со временем.
     */
    public function cleaningPeoplesOfUselessTimestamp(PeopleCollectionDTO $data): PeopleCollectionDTO
    {
        /** @var PeopleDTO $people */
        foreach ($data->getPeoples() as $i => &$people) {
            /** @var LocationDTO $location */
            $locations = $people->getLocations();
            foreach ($locations as $j => $location) {
                /*
                 * По нормальному надо брать данные для каждой зоны и для каждого времени года, но так как времени нету,
                 * то делаем все для одного единственного промежутка времени
                 */
                $testArray = [
                    'min' => 18,
                    'max' => 6,
                ];
                $hour = (new \DateTime())->setTimestamp($location->getTimestamp())->format('H');

                // если время попадает в дневной промежуток времени, то удаляем локацию
                if ((int) $hour < $testArray['min'] && (int) $hour > $testArray['max']) {
                    unset($locations[$j]);
                }
            }
            $people->setLocations($locations);
        }

        return $data;
    }

    /**
     * Очистка от бесполезных данных передвижения людей связанных с лишними локациями.
     *
     * @throws \Exception
     */
    public function cleaningPeoplesOfUselessLocations(
        PeopleCollectionDTO $dataPeoples,
        FeatureCollectionDTO $dataFeatures,
        $time = 60 * 60
    ): PeopleCollectionDTO {
        foreach ($dataPeoples->getPeoples() as &$people) {
            /** @var LocationDTO[] $locations */
            $locations = $people->getLocations();
            foreach ($locations as $t => $tValue) {
                $coordsT = [$tValue->getLongitude(), $tValue->getLatitude()];
                $streetNameT = $this->calculationGeometryWithLines->getClosestLineToGivenCoords(
                    $dataFeatures->getFeatures(),
                    $coordsT
                )
                    ->getProperties()->getNameStreet();
                foreach ($locations as $j => $jValue) {
                    $coordsI = [$jValue->getLongitude(), $jValue->getLatitude()];
                    $streetNameI =
                        $this->calculationGeometryWithLines->getClosestLineToGivenCoords(
                            $dataFeatures->getFeatures(),
                            $coordsI
                        )->getProperties()->getNameStreet();
                    if ($streetNameI === $streetNameT && $t !== $j) {
                        if ($time >= abs($tValue->getTimestamp() - $jValue->getTimestamp())) {
                            unset($locations[$j]);
                        }
                    }
                }
            }
            $people->setLocations($locations);
        }

        return $dataPeoples;
    }
}
