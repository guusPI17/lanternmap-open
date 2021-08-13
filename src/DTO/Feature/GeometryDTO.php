<?php

namespace App\DTO\Feature;

use JMS\Serializer\Annotation as Serializer;

class GeometryDTO
{
    /**
     * @Serializer\Type("string")
     */
    private $type;

    /**
     * @Serializer\Type("array")
     */
    private $coordinates;

    public function __construct(array $coordinates = [], string $type = 'Point')
    {
        $this->type = $type;
        $this->coordinates = $coordinates;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    public function setCoordinates(array $coordinates): void
    {
        $this->coordinates = $coordinates;
    }
}
