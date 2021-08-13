<?php

namespace App\DTO\Feature;

use JMS\Serializer\Annotation as Serializer;

class PropertiesDTO
{
    /**
     * @Serializer\Type("string")
     */
    private $nameStreet;

    /**
     * @Serializer\Type("string")
     */
    private $nameLantern;

    /**
     * @Serializer\Type("float")
     */
    private $height;

    /**
     * @Serializer\Type("float")
     */
    private $price;

    /**
     * @Serializer\Type("float")
     */
    private $length;

    /**
     * @Serializer\Type("float")
     */
    private $width;

    /**
     * @Serializer\Type("string")
     */
    private $classObject;

    /**
     * @Serializer\Type("float")
     */
    private $priority;

    public function getNameStreet(): string
    {
        return $this->nameStreet;
    }

    public function setNameStreet(string $nameStreet): void
    {
        $this->nameStreet = $nameStreet;
    }

    public function getNameLantern(): string
    {
        return $this->nameLantern;
    }

    public function setNameLantern(string $nameLantern): void
    {
        $this->nameLantern = $nameLantern;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function setHeight(float $height): void
    {
        $this->height = $height;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function setLength(float $length): void
    {
        $this->length = $length;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setWidth(string $width): void
    {
        $this->width = $width;
    }

    public function getClassObject(): string
    {
        return $this->classObject;
    }

    public function setClassObject(string $classObject): void
    {
        $this->classObject = $classObject;
    }

    public function getPriority(): float
    {
        return $this->priority;
    }

    public function setPriority(float $priority): void
    {
        $this->priority = $priority;
    }
}
