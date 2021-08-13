<?php

namespace App\DTO\Curve;

use JMS\Serializer\Annotation as Serializer;

class CurveCoefTableDTO
{
    /**
     * @Serializer\Type("float")
     */
    private $minHeight;

    /**
     * @Serializer\Type("float")
     */
    private $minLightFlow;

    /**
     * @Serializer\Type("float")
     */
    private $maxLightFlow;

    public function getMinHeight(): float
    {
        return $this->minHeight;
    }

    public function setMinHeight(float $minHeight): void
    {
        $this->minHeight = $minHeight;
    }

    public function getMinLightFlow(): float
    {
        return $this->minLightFlow;
    }

    public function setMinLightFlow(float $minLightFlow): void
    {
        $this->minLightFlow = $minLightFlow;
    }

    public function getMaxLightFlow(): float
    {
        return $this->maxLightFlow;
    }

    public function setMaxLightFlow(float $maxLightFlow): void
    {
        $this->maxLightFlow = $maxLightFlow;
    }
}
