<?php

namespace App\DTO\People;

use JMS\Serializer\Annotation as Serializer;

class PeopleDTO
{
    /**
     * @Serializer\Type("int")
     */
    private $id;

    /**
     * @Serializer\Type("array<App\DTO\People\LocationDTO>")
     */
    private $locations;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function setLocations(array $locations): void
    {
        $this->locations = $locations;
    }
}
