<?php

namespace App\DataFixtures;

use App\Entity\Curve;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurveFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $data = [
            [
                'name' => 'полуширокое',
                'coefTable' => '[{"min_height":7.0,"min_light_flow":0,"max_light_flow":6000},{"min_height":7.5,"min_light_flow":6000,"max_light_flow":10000},{"min_height":8.0,"min_light_flow":10000,"max_light_flow":20000},{"min_height":9.0,"min_light_flow":200000,"max_light_flow":30000},{"min_height":10,"min_light_flow":30000,"max_light_flow":40000},{"min_height":11.5,"min_light_flow":40000,"max_light_flow":100000}]',
            ],
            [
                'name' => 'широкое',
                'coefTable' => '[{"min_height":7.5,"min_light_flow":0,"max_light_flow":6000},{"min_height":8.5,"min_light_flow":6000,"max_light_flow":10000},{"min_height":9.5,"min_light_flow":10000,"max_light_flow":20000},{"min_height":10.5,"min_light_flow":200000,"max_light_flow":30000},{"min_height":11.5,"min_light_flow":30000,"max_light_flow":40000},{"min_height":13.0,"min_light_flow":40000,"max_light_flow":100000}]',
            ],
        ];
        foreach ($data as $value) {
            $curve = $this->createCurve($value);
            $manager->persist($curve);
        }
        $manager->flush();
    }

    private function createCurve(array $data): Curve
    {
        $curve = new Curve();

        $curve->setName($data['name']);
        $curve->setCoefTable($data['coefTable']);

        $this->addReference($data['name'], $curve);

        return $curve;
    }
}
