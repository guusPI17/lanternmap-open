<?php

namespace App\DataFixtures;

use App\Entity\Locality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocalityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $data = [
            [
                'name' => 'Становое',
                'dataMovement' => null,
                'latitude' => [
                    52.7497, 52.7718,
                ],
                'longitude' => [
                    38.3094, 38.3793,
                ],
                'timezone' => 'Europe/Moscow',
            ],
        ];
        foreach ($data as $value) {
            $locality = $this->createLocality($value);
            $manager->persist($locality);
        }
        $manager->flush();
    }

    private function createLocality(array $data): Locality
    {
        $locality = new Locality();

        $locality->setName($data['name']);
        $locality->setDataMovement($data['dataMovement']);
        $locality->setLatitude($data['latitude']);
        $locality->setLongitude($data['longitude']);
        $locality->setTimezone($data['timezone']);

        $this->addReference($data['name'], $locality);

        return $locality;
    }
}
