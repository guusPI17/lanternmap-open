<?php

namespace App\DTO\Lantern;

use JMS\Serializer\Annotation as Serializer;

class MinIlluminationDTO
{
    /**
     * @Serializer\Type("int")
     */
    private $value;

    /**
     * @Serializer\Type("array")
     */
    private $coefE;

    /**
     * @Serializer\Type("array")
     */
    private $coefN;

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getCoefE(): array
    {
        return $this->coefE;
    }

    public function setCoefE(array $coefE): void
    {
        $this->coefE = $coefE;
    }

    public function getCoefN(): array
    {
        return $this->coefN;
    }

    public function setCoefN(array $coefN): void
    {
        $this->coefN = $coefN;
    }
}
