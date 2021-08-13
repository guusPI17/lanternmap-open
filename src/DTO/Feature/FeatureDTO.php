<?php

namespace App\DTO\Feature;

use JMS\Serializer\Annotation as Serializer;

class FeatureDTO
{
    /**
     * @Serializer\Type("string")
     */
    private $type;

    /**
     * @Serializer\Type("App\DTO\Feature\GeometryDTO")
     */
    private $geometry;

    /**
     * @Serializer\Type("App\DTO\Feature\PropertiesDTO")
     */
    private $properties;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getGeometry(): object
    {
        return $this->geometry;
    }

    public function setGeometry(object $geometry): void
    {
        $this->geometry = $geometry;
    }

    public function getProperties(): object
    {
        return $this->properties;
    }

    public function setProperties(object $properties): void
    {
        $this->properties = $properties;
    }
}
