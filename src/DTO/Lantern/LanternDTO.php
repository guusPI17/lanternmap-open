<?php

namespace App\DTO\Lantern;

use JMS\Serializer\Annotation as Serializer;

class LanternDTO
{
    /**
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @Serializer\Type("string")
     */
    private $type;

    /**
     * @Serializer\Type("string")
     */
    private $curve;

    /**
     * @Serializer\Type("float")
     */
    private $price;

    /**
     * @Serializer\Type("int")
     */
    private $lightFlow;

    /**
     * @Serializer\Type("array<App\DTO\Lantern\MinIlluminationDTO>")
     */
    private $minIlluminations;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getCurve(): string
    {
        return $this->curve;
    }

    public function setCurve(string $curve): void
    {
        $this->curve = $curve;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getLightFlow(): int
    {
        return $this->lightFlow;
    }

    public function getMinIlluminations(): array
    {
        return $this->minIlluminations;
    }

    public function setMinIlluminations(array $minIlluminations): void
    {
        $this->minIlluminations = $minIlluminations;
    }
}
