<?php

namespace App\DTO\LanternType;

use JMS\Serializer\Annotation as Serializer;

class TableYhDTO
{
    /**
     * @Serializer\Type("float")
     */
    private $yH;

    /**
     * @Serializer\Type("array")
     */
    private $n;

    public function getYh(): float
    {
        return $this->yH;
    }

    public function setYh(float $yH): void
    {
        $this->yH = $yH;
    }

    public function getN(): array
    {
        return $this->n;
    }

    public function setN(array $n): void
    {
        $this->n = $n;
    }
}
