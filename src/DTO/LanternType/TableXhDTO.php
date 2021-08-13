<?php

namespace App\DTO\LanternType;

use JMS\Serializer\Annotation as Serializer;

class TableXhDTO
{
    /**
     * @Serializer\Type("float")
     */
    private $xH;

    /**
     * @Serializer\Type("float")
     */
    private $e;

    /**
     * @Serializer\Type("float")
     */
    private $p3;

    public function getXh(): float
    {
        return $this->xH;
    }

    public function setXh(float $xH): void
    {
        $this->xH = $xH;
    }

    public function getE(): float
    {
        return $this->e;
    }

    public function setE(float $e): void
    {
        $this->e = $e;
    }

    public function getP3(): float
    {
        return $this->p3;
    }

    public function setP3(float $p3): void
    {
        $this->p3 = $p3;
    }
}
