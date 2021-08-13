<?php

namespace App\DataFixtures;

use App\Entity\Map;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MapFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            LocalityFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $data = [
            [
                'name' => 'Карта #1',
                'data' => '{"type":"FeatureCollection","features":[]}',
                'report' => null,
                'user' => $this->getReference('adminPI17'),
                'locality' => $this->getReference('Становое'),
            ],
            [
                'name' => 'Вторая карта',
                'data' => '{"type":"FeatureCollection","features":[]}',
                'report' => null,
                'user' => $this->getReference('adminPI17'),
                'locality' => $this->getReference('Становое'),
            ],
        ];
        foreach ($data as $value) {
            $map = $this->createUser($value);
            $manager->persist($map);
        }
        $manager->flush();
    }

    private function createUser(array $data): Map
    {
        $map = new Map();

        $map->setName($data['name']);
        $map->setData($data['data']);
        $map->setReport($data['report']);
        $map->setUserAccount($data['user']);
        $map->setLocality($data['locality']);

        return $map;
    }
}
