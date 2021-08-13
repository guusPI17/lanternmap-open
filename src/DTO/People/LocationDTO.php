<?php

namespace App\DTO\People;

use JMS\Serializer\Annotation as Serializer;

class LocationDTO
{
    /**
     * @Serializer\Type("int")
     */
    private $timestamp;

    /**
     * @Serializer\Type("float")
     */
    private $latitude;

    /**
     * @Serializer\Type("float")
     */
    private $longitude;

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }
}
