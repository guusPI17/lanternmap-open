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

class CalculationStreetLights
{
    private const SAFETY_FACTOR = 1.5;
    private const PERCENTAGE_DIFFERENCE = 10;

    private $em;

    private $serializer;

    public function __construct(EntityManager $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public function calculation(RatingCollectionDTO $ratingCollectionDTO): RatingCollectionDTO
    {
        $featuresRating = $ratingCollectionDTO->getFeatures();

        /** @var FeatureRatingDTO $featureRating */
        foreach ($featuresRating as $featureRating) {
            /** @var FeatureDTO $feature */
            $feature = $featureRating->getFeature();
            /** @var PropertiesDTO $properties */
            $properties = $feature->getProperties();
            // средняя освещенность по ГОСТу
            $averageIllumination = $this->em->getRepository(StreetClass::class)
                ->findOneBy(['name' => $properties->getClassObject()])->getAverageIllumination();

            // ширина дороги
            $width = $properties->getWidth();
            // длина дороги
            $length = $properties->getLength();

            $lanterns = $this->em->getRepository(Lantern::class)->findAll();
            // минимальная освещенность
            $minIllumination = $this->getMinIllumination($averageIllumination);

            // минимальная стоимость освещения для улицы
            $minTotalCostStreet = PHP_FLOAT_MAX;

            // оптмиальные значений
            $optimalHeight = 0;
            $optimalStep = 0;
            $optimalLantern = null;
            $optimalCountLantern = 0;

            foreach ($lanterns as $lantern) {
                // таблица кривой
                /** @var CurveCoefTableDTO[] $curveCoefTables */
                $curveCoefTables = $this->serializer->deserialize(
                    $lantern->getCurve()->getCoefTable(),
                    'array<App\DTO\Curve\CurveCoefTableDTO>',
                    'json'
                );

                // минимальная возможная высота устаноки фонаря
                $minHeight = $this->getMinHeight($curveCoefTables, $lantern->getLightFlow());

                // таблица типа
                /** @var LanternTypeCoefTableDTO $lanternTypeCoefTable */
                $lanternTypeCoefTable = $this->serializer->deserialize(
                    $lantern->getType()->getCoefTable(),
                    LanternTypeCoefTableDTO::class,
                    'json'
                );
                $tableXh = $lanternTypeCoefTable->getTableXh();
                $tableYh = $lanternTypeCoefTable->getTableYh();

                // световой поток
                $lightFlow = $lantern->getLightFlow();
                // график изолюкс
                $isoluxs = $this->serializer->deserialize(
                    $lantern->getIsolux(),
                    'array<App\DTO\Lantern\MinIlluminationDTO>',
                    'json'
                );
                // высота
                $h = $minHeight;

                $x = $width / 2; // формула
                $xH = $x / $h; // формула

                // находим из таблицы подходящее значение
                $suitableTableXh = $this->getSuitableTableXh($tableXh, $xH);

                $e = $suitableTableXh->getE(); // таблица
                $p3 = $suitableTableXh->getP3(); // таблица
                $sumE = $this->getTotalRelativeIllumination( // формула
                    $minIllumination,
                    self::SAFETY_FACTOR,
                    $h,
                    $p3,
                    $lightFlow
                );
                $E = $sumE / 2; // формула
                $n = $this->getCoefNonScheduleIsolux($isoluxs, $E, $e);
                // находим из таблицы подходящее значение
                $suitableTableYh = $this->getSuitableTableYh($tableYh, $n);

                $yH = $suitableTableYh->getYh(); // таблица
                $y = $h * $yH; // формула

                $stepLantern = 2 * $y; // формула по нахождению шага светильника
                $countLantern = floor($length / $stepLantern); // формула нахождения количества фонарей для улицы
                //var_dump($lantern->getLightFlow().":".$h .":".$countLantern);
                $totalCostStreet = $countLantern * $lantern->getPrice(); // общая стоимость всех фонарей

                // если новая закупка лучше
                if ($totalCostStreet < $minTotalCostStreet) {
                    $minTotalCostStreet = $totalCostStreet;
                    $optimalHeight = $h;
                    $optimalStep = $stepLantern;
                    $optimalLantern = $lantern;
                    $optimalCountLantern = $countLantern;
                } else {
                    // если новая закупку хуже
                    break;
                }
            }

            $featureRating->setUse(true);
            $featureRating->setOptimalHeightLantern($optimalHeight);
            $featureRating->setOptimalStepLantern($optimalStep);
            $featureRating->setOptimalTotalCost($minTotalCostStreet);
            $arrayLanterns = [];
            for ($i = 0; $i < $optimalCountLantern; ++$i) {
                $newLantern = new LanternDTO();
                $newLantern->setCurve($optimalLantern->getCurve()->getName());
                $newLantern->setType($optimalLantern->getType()->getName());
                $newLantern->setPrice($optimalLantern->getPrice());
                $newLantern->setName($optimalLantern->getName());
                $arrayLanterns[] = $newLantern;
            }
            $featureRating->setLanterns($arrayLanterns);
        }
        $ratingCollectionDTO->setFeatures($featuresRating);

        return $ratingCollectionDTO;
    }

    /**
     * Возвращает из таблицы типов подходящуюю строу по заданному n.
     */
    private function getSuitableTableYh(array $tableYh, float $n): TableYhDTO
    {
        $arraySuitableN = [];
        /** @var TableYhDTO $table */
        foreach ($tableYh as $key => $table) {
            $closest = null;
            $index = null;
            foreach ($table->getN() as $item) {
                if (null === $closest || abs($n - $closest) > abs($item - $n)) {
                    $closest = $item;
                    $index = $key;
                }
            }
            $arraySuitableN[$index] = $closest;
        }

        $closest = null;
        $index = null;
        foreach ($arraySuitableN as $key => $item) {
            if (null === $closest || abs($n - $closest) > abs($item - $n)) {
                $closest = $item;
                $index = $key;
            }
        }

        return $tableYh[$index];
    }

    /**
     * Возвращает коэффциент n по графику условных изолюкс при заданных параметрах.
     *
     * @param array $isoluxx
     *
     * @throws \Exception
     */
    private function getCoefNonScheduleIsolux(array $isoluxs, float $E, float $e): float
    {
        // Возвращает подходяющую изолюксу по задданному освещенносте.
        /** @var MinIlluminationDTO $suitableIsolux */
        $suitableIsoluxValue = null;
        $index = null;
        foreach ($isoluxs as $key => $item) {
            if (null === $suitableIsoluxValue || abs($E - $suitableIsoluxValue) > abs($item->getValue() - $E)) {
                $suitableIsoluxValue = $item->getValue();
                $index = $key;
            }
        }
        $suitableIsolux = $isoluxs[$index];

        $coefE = null;
        $index = null;
        foreach ($suitableIsolux->getCoefE() as $key => $item) {
            if (null === $coefE || abs($e - $coefE) > abs($item - $e)) {
                $coefE = $item;
                $index = $key;
            }
        }

        return $suitableIsolux->getCoefN()[$index];
    }

    /**
     * Возвращает минимальную освещенность по ГОСТу.
     *
     * @param float $averageIllumination средняя освещенность
     */
    private function getMinIllumination(float $averageIllumination): float
    {
        return $averageIllumination - (($averageIllumination / 100) * self::PERCENTAGE_DIFFERENCE);
    }

    /**
     * Находим суммарную относительную освещенность по формуле.
     *
     * @param float $Eh нормативная минимальная освещенность
     * @param float $Ks коэффициент запаса
     * @param float $h  высота установки
     * @param float $p3 табличное значение
     * @param float $Fl световой поток
     */
    private function getTotalRelativeIllumination(float $Eh, float $Ks, float $h, float $p3, float $Fl): float
    {
        return (1000 * $Eh * $Ks * ($h ** 2) * $p3) / ($Fl);
    }

    /**
     * Возвращает из таблицы типов подходящуюю строу по заданному xH.
     *
     * @throws \Exception
     */
    private function getSuitableTableXh(array $tableXh, float $xH): TableXhDTO
    {
        $suitableXh = null;
        $index = null;
        foreach ($tableXh as $key => $item) {
            if (null === $suitableXh || abs($xH - $suitableXh) > abs($item->getXh() - $xH)) {
                $suitableXh = $item->getXh();
                $index = $key;
            }
        }

        return $tableXh[$index];
    }

    /**
     * Возвращает минимальную возможную длину установки осветильного прибора для
     * заданной таблицы кривой(по ГОСТу) и светового потока.
     *
     * @throws \Exception
     */
    private function getMinHeight(array $curveCoefTables, float $lanternLightFlow): float
    {
        foreach ($curveCoefTables as $curveCoefTable) {
            if ($lanternLightFlow >= $curveCoefTable->getMinLightFlow()
                && $lanternLightFlow <= $curveCoefTable->getMaxLightFlow()) {
                return $curveCoefTable->getMinHeight();
            }
        }
        throw new \Exception('Отсутствует подходящий фонарь из списка возможных', 400);
    }
}