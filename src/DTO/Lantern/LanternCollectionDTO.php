<?php

namespace App\DTO\Lantern;

use JMS\Serializer\Annotation as Serializer;

class LanternCollectionDTO
{
    /**
     * @Serializer\Type("array<App\DTO\Lantern\LanternDTO>")
     */
    private $lanterns;

    public function getLanterns(): array
    {
        return $this->lanterns;
    }

    public function setLanterns(array $lanterns): void
    {
        $this->lanterns = $lanterns;
    }
}
