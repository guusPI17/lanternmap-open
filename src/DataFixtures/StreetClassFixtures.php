<?php

namespace App\DataFixtures;

use App\Entity\StreetClass;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StreetClassFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $data = [
            [
                'name' => 'А1',
                'averageIllumination' => 30,
            ],
            [
                'name' => 'А2',
                'averageIllumination' => 20,
            ],
            [
                'name' => 'А3',
                'averageIllumination' => 20,
            ],
            [
                'name' => 'А4',
                'averageIllumination' => 20,
            ],
            [
                'name' => 'Б1',
                'averageIllumination' => 20,
            ],
            [
                'name' => 'Б2',
                'averageIllumination' => 12,
            ],
            [
                'name' => 'В1',
                'averageIllumination' => 15,
            ],
            [
                'name' => 'В2',
                'averageIllumination' => 10,
            ],
            [
                'name' => 'В3',
                'averageIllumination' => 6,
            ],
            [
                'name' => 'П1',
                'averageIllumination' => 20,
            ],
            [
                'name' => 'П2',
                'averageIllumination' => 10,
            ],
            [
                'name' => 'П3',
                'averageIllumination' => 6,
            ],
            [
                'name' => 'П4',
                'averageIllumination' => 4,
            ],
            [
                'name' => 'П5',
                'averageIllumination' => 2,
            ],
            [
                'name' => 'П6',
                'averageIllumination' => 1,
            ],
        ];
        foreach ($data as $value) {
            $streetClass = $this->createStreetClass($value);
            $manager->persist($streetClass);
        }
        $manager->flush();
    }

    private function createStreetClass(array $data): StreetClass
    {
        $streetClass = new StreetClass();

        $streetClass->setName($data['name']);
        $streetClass->setAverageIllumination($data['averageIllumination']);

        $this->addReference($data['name'], $streetClass);

        return $streetClass;
    }
}
