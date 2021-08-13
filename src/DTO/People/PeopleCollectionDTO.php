<?php

namespace App\DTO\People;

use JMS\Serializer\Annotation as Serializer;

class PeopleCollectionDTO
{
    /**
     * @Serializer\Type("array<App\DTO\People\PeopleDTO>")
     */
    private $peoples;

    public function getPeoples(): array
    {
        return $this->peoples;
    }

    public function setPeoples(array $peoples): void
    {
        $this->peoples = $peoples;
    }
}
