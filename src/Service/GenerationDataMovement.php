<?php

namespace App\Service;

use App\DTO\Feature\FeatureCollectionDTO;
use App\DTO\Feature\FeatureDTO;
use App\DTO\Feature\GeometryDTO;
use App\DTO\Feature\PropertiesDTO;
use App\DTO\People\LocationDTO;
use App\DTO\People\PeopleCollectionDTO;
use App\DTO\People\PeopleDTO;
use JMS\Serializer\SerializerInterface;

class GenerationDataMovement
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array $restrictions Ограничения для генерации данных
     *
     * @return string json content
     *
     * @throws \Exception
     */
    public function generation(array $restrictions): PeopleCollectionDTO
    {
        $peopleCollectionDTO = new PeopleCollectionDTO();
        $peoples = [];

        // время для генерации
        $year = $restrictions['timestamp']['minYear'];
        $dateTimeStart = "01-01-$year 00:00:00";
        $timestampStart = (new \DateTime($dateTimeStart))->getTimestamp();
        $timestampEnd = (new \DateTime())->getTimestamp();

        $countPeople = 500;
        for ($i = 0; $i < $countPeople; ++$i) {
            $people = new PeopleDTO();
            $people->setId($i);
            $locations = [];

            $countLocations = 20;
            for ($j = 0; $j < $countLocations; ++$j) {
                $location = new LocationDTO();

                $timestamp = rand($timestampEnd, $timestampStart);
                $location->setTimestamp($timestamp); // временная метка

                $latitude = mt_rand($restrictions['latitude']['min'], $restrictions['latitude']['max']);
                $location->setLatitude($latitude / $restrictions['coef']); // широта

                $longitude = mt_rand($restrictions['longitude']['min'], $restrictions['longitude']['max']);
                $location->setLongitude($longitude / $restrictions['coef']); // долгота

                $locations[] = $location;
            }

            // проверка интервала времени по ограничению
            foreach ($locations as $locationI) {
                while (true) {
                    $check = true;
                    foreach ($locations as &$locationJ) {
                        $divTimestamp = abs($locationI->getTimestamp() - $locationJ->getTimestamp());
                        if ($divTimestamp < 1 && $locationI != $locationJ) {
                            $check = false;
                            $timestamp = rand($timestampEnd, $timestampStart);
                            $locationJ->setTimestamp($timestamp); // временная метка
                            break; // проверка на ограничение интервала не выполнено
                        }
                    }
                    if ($check) {
                        break; // проверка на ограничение интервала выполнено
                    }
                }
            }

            $people->setLocations($locations);
            $peoples[] = $people;
        }
        $peopleCollectionDTO->setPeoples($peoples);

        return $peopleCollectionDTO;
    }

    /**
     * Конвертирование из PeopleCollectionDTO в FeatureCollectionDTO.
     *
     * @param string $jsonPeoples PeopleCollectionDTO в json строке
     */
    public function convertPeoplesToGeoJson(string $jsonPeoples): FeatureCollectionDTO
    {
        /** @var PeopleCollectionDTO $peopleCollectionDTO */
        $peopleCollectionDTO = $this->serializer->deserialize(
            $jsonPeoples,
            PeopleCollectionDTO::class,
            'json'
        );

        $featureCollectionDTO = new FeatureCollectionDTO();
        $featureCollectionDTO->setType('FeatureCollection');

        $features = [];
        foreach ($peopleCollectionDTO->getPeoples() as $people) {
            /** @var LocationDTO $location */
            foreach ($people->getLocations() as $location) {
                $feature = new FeatureDTO();
                $feature->setType('Feature');
                $feature->setGeometry(
                    (new GeometryDTO([$location->getLongitude(), $location->getLatitude()]))
                );
                $feature->setProperties((new PropertiesDTO()));

                $features[] = $feature;
            }
        }
        $featureCollectionDTO->setFeatures($features);

        return $featureCollectionDTO;
    }
}
