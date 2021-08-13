<?php

namespace App\Service;

use App\DTO\People\LocationDTO;
use App\DTO\People\PeopleCollectionDTO;
use App\DTO\People\PeopleDTO;

class DataAveraging
{
    /**
     * Усреднение данны по выбранному формату даты.
     *
     * @param string $formatDate Формат даты (i,s,H,d,m,Y)
     *
     * @throws \Exception
     */
    public function averagingOneDate(PeopleCollectionDTO $peopleCollectionDTO, string $formatDate): PeopleCollectionDTO
    {
        $newPeopleCollectionDTO = new PeopleCollectionDTO();
        $newPeopleCollectionDTO->setPeoples($peopleCollectionDTO->getPeoples());
        $newPeoples = [];

        /** @var PeopleDTO $people */
        foreach ($peopleCollectionDTO->getPeoples() as $people) {
            $newPeople = new PeopleDTO();
            $newPeople->setId($people->getId());
            $newLocations = [];

            $locations = $people->getLocations();
            // сортировка по временным меткам в порядке возрастания
            usort($locations, static function ($object1, $object2) {
                return $object1->getTimestamp() > $object2->getTimestamp();
            });

            $startDay = '';
            $sumCoords = [];
            $countLocation = 0;
            $check = true;
            /** @var LocationDTO $location */
            foreach ($locations as $location) {
                if ($check) {
                    $startDay = (new \DateTime())->setTimestamp($location->getTimestamp())->format($formatDate);
                    $sumCoords = [
                        'longitude' => $location->getLongitude(),
                        'latitude' => $location->getLatitude(),
                    ];
                    $countLocation = 1;
                    $check = false;
                    continue;
                }
                $day = (new \DateTime())->setTimestamp($location->getTimestamp())->format($formatDate);
                if ($day === $startDay) {
                    $sumCoords['longitude'] += $location->getLongitude();
                    $sumCoords['latitude'] += $location->getLatitude();
                    ++$countLocation;
                } else {
                    $newLocation = new LocationDTO();
                    $newLocation->setLongitude($sumCoords['longitude'] / $countLocation);
                    $newLocation->setLatitude($sumCoords['latitude'] / $countLocation);
                    $newLocation->setTimestamp($location->getTimestamp());
                    $newLocations[] = $newLocation;
                    $check = true;
                }
            }
            $newPeople->setLocations($newLocations);
            $newPeoples[] = $newPeople;
        }
        $newPeopleCollectionDTO->setPeoples($newPeoples);

        return $newPeopleCollectionDTO;
    }
}
