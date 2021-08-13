<?php

namespace App\Service;

use App\DTO\Feature\FeatureCollectionDTO;
use App\DTO\Feature\FeatureDTO;
use App\DTO\Feature\FeatureRatingDTO;
use App\DTO\Feature\RatingCollectionDTO;
use App\DTO\People\LocationDTO;
use App\DTO\People\PeopleCollectionDTO;
use App\DTO\People\PeopleDTO;
use App\Entity\StreetClass;
use Doctrine\ORM\EntityManager;

class StreetRatingCalculation
{
    private $calculationGeometryWithLines;
    private $em;
    private $featureCollectionDTO;
    private $peopleCollectionDTO;

    public function __construct(CalculationGeometryWithLines $calculationGeometryWithLines, EntityManager $em)
    {
        $this->calculationGeometryWithLines = $calculationGeometryWithLines;
        $this->em = $em;
    }

    /**
     * Вычисление рейтинга улицы.
     *
     * @throws \Exception
     */
    public function calculation(
        PeopleCollectionDTO $peopleCollectionDTO,
        FeatureCollectionDTO $featureCollectionDTO
    ): RatingCollectionDTO {
        $this->peopleCollectionDTO = $peopleCollectionDTO;
        $this->featureCollectionDTO = $featureCollectionDTO;

        // получение рейтинга по количеству людей
        $ratingByPeople = $this->getRatingByPeople();

        // получение общего рейтинга по улицам
        /** @var FeatureRatingDTO[] $ratingOverall */
        $ratingOverall = $this->getRatingOverall($ratingByPeople);

        // проверка на погрешность в рейтинге (определение равнозначных улиц)
        $ratingOverall = $this->getRatingAfterCheckError($ratingOverall);

        $ratingCollectionDTO = new RatingCollectionDTO();
        $ratingCollectionDTO->setFeatures($ratingOverall);

        return $ratingCollectionDTO;
    }

    /**
     * Возвращает рейтинг улиц взависимости от количества принадлежащих людей.
     *
     * @throws \Exception
     */
    private function getRatingByPeople(): array
    {
        $ratingByPeople = [];

        // составляем список улиц и начальное количество принадлежащих людей
        /** @var FeatureDTO $feature */
        foreach ($this->featureCollectionDTO->getFeatures() as $feature) {
            $ratingByPeople[$feature->getProperties()->getNameStreet()] = 0;
        }

        if (count($ratingByPeople) !== count($this->featureCollectionDTO->getFeatures())) {
            throw new \RuntimeException('У вас имеются улицы с одинаковым названием.' . 'Названия должны быть уникальны.', 400);
        }

        // считаем количество людей для каждой улицы
        /** @var PeopleDTO $people */
        foreach ($this->peopleCollectionDTO->getPeoples() as $people) {
            /** @var LocationDTO $location */
            foreach ($people->getLocations() as $location) {
                $coords = [$location->getLongitude(), $location->getLatitude()];
                /** @var FeatureDTO $feature */
                $feature = $this->calculationGeometryWithLines->getClosestLineToGivenCoords(
                    $this->featureCollectionDTO->getFeatures(),
                    $coords
                );
                ++$ratingByPeople[$feature->getProperties()->getNameStreet()];
            }
        }

        return $ratingByPeople;
    }

    /**
     * Возвращает рейтинг после проверки на погрешность (определение равнозначных улиц).
     */
    private function getRatingAfterCheckError(array $ratingOverall): array
    {
        for ($i = 0, $iMax = count($ratingOverall); $i < $iMax; ++$i) {
            if ($i + 1 < $iMax) {
                // формула нахождения процентного отличия двух величин
                $percentDiff = $this->getPercentDiff(
                    $ratingOverall[$i + 1]->getRating(),
                    $ratingOverall[$i]->getRating()
                );
                // если отличие меньше или равняется 5%
                if (5 >= $percentDiff) {
                    $ratingOverall[$i + 1]->setRating($ratingOverall[$i]->getRating());
                }
            }
        }

        // сортировка по убыванию по двум параметрам (rating, priority)
        usort($ratingOverall, static function ($object1, $object2) {
            return $object1->getRating() > $object2->getRating() ? -1
                : ($object1->getRating() < $object2->getRating() ? 1
                    : ($object1->getFeature()->getProperties()->getPriority() > $object2->getFeature()->getProperties()->getPriority() ? -1
                        : ($object1->getFeature()->getProperties()->getPriority() < $object2->getFeature()->getProperties()->getPriority() ? 1
                            : 0)));
        });

        return $ratingOverall;
    }

    /**
     * Возвращает общий рейтинг улиц.
     *
     * @param array $ratingByPeople Рейтинг улиц после определения количества принадлежщий к ней людей
     */
    private function getRatingOverall(array $ratingByPeople): array
    {
        // считаем рейтинг для каждой улицы
        /** @var FeatureDTO $feature */
        foreach ($this->featureCollectionDTO->getFeatures() as $feature) {
            $featureRatingDTO = new FeatureRatingDTO();
            $featureRatingDTO->setFeature($feature);

            // получаем минимальную освещенность по классу улицы из БД
            $averageIllumination = $this->em->getRepository(StreetClass::class)
                ->findOneBy(['name' => $feature->getProperties()->getClassObject()])->getAverageIllumination();
            $countPeople = $ratingByPeople[$feature->getProperties()->getNameStreet()];

            // формула нахождения рейтинга улицы
            $rating = $this->getStreetRating($countPeople, $averageIllumination);
            if(0 == $rating){
                $rating = 0.001;
            }

            $featureRatingDTO->setRating($rating);
            $featureRatingDTO->setCountPeople($countPeople);
            $ratingOverall[] = $featureRatingDTO;
        }

        // сортировка по рейтингу в порядке убывания
        usort($ratingOverall, static function ($object1, $object2) {
            return $object1->getRating() < $object2->getRating();
        });

        return $ratingOverall;
    }

    /**
     * Нахождение рейтинга улицы по формлеу.
     *
     * @param int   $countPeople         Количество людей
     * @param float $averageIllumination Средняя освещенность
     */
    private function getStreetRating(int $countPeople, float $averageIllumination): float
    {
        return $countPeople * ($averageIllumination * (10 ** -1));
    }

    /**
     * Нахождение процентного отличия между двумя величинами по формуле.
     *
     * @param float $val1 Первая величина меньшая чем val2
     * @param float $val2 Вторая величина большая чем val1
     */
    private function getPercentDiff(float $val1, float $val2): int
    {
        return 100 - (($val1 * 100) / $val2);
    }
}
