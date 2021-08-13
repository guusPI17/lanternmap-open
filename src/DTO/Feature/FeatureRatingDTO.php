<?php

namespace App\DTO\Feature;

use JMS\Serializer\Annotation as Serializer;

class FeatureRatingDTO
{
    /**
     * @Serializer\Type("bool")
     */
    private $use;

    /**
     * @Serializer\Type("float")
     */
    private $rating;

    /**
     * @Serializer\Type("float")
     */
    private $optimalStepLantern;

    /**
     * @Serializer\Type("float")
     */
    private $optimalTotalCost;

    /**
     * @Serializer\Type("float")
     */
    private $optimalHeightLantern;

    /**
     * @Serializer\Type("array<App\DTO\Feature\LanternDTO>")
     */
    private $lanterns;

    /**
     * @Serializer\Type("int")
     */
    private $countPeople;

    /**
     * @Serializer\Type("App\DTO\Feature\FeatureDTO")
     */
    private $feature;

    public function getUse(): bool
    {
        return $this->use;
    }

    public function setUse(bool $use): void
    {
        $this->use = $use;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }

    public function getOptimalStepLantern(): ?float
    {
        return $this->optimalStepLantern;
    }

    public function setOptimalStepLantern(float $optimalStepLantern): void
    {
        $this->optimalStepLantern = $optimalStepLantern;
    }

    public function getOptimalTotalCost(): ?float
    {
        return $this->optimalTotalCost;
    }

    public function setOptimalTotalCost(float $optimalTotalCost): void
    {
        $this->optimalTotalCost = $optimalTotalCost;
    }

    public function getOptimalHeightLantern(): ?float
    {
        return $this->optimalHeightLantern;
    }

    public function setOptimalHeightLantern(float $optimalHeightLantern): void
    {
        $this->optimalHeightLantern = $optimalHeightLantern;
    }

    public function getLanterns(): ?array
    {
        return $this->lanterns;
    }

    public function setLanterns(array $lanterns): void
    {
        $this->lanterns = $lanterns;
    }

    public function getCountPeople(): int
    {
        return $this->countPeople;
    }

    public function setCountPeople(int $countPeople): void
    {
        $this->countPeople = $countPeople;
    }

    public function getFeature(): object
    {
        return $this->feature;
    }

    public function setFeature(object $feature): void
    {
        $this->feature = $feature;
    }
}
