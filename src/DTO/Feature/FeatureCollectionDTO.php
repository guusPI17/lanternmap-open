<?php

namespace App\DTO\Feature;

use JMS\Serializer\Annotation as Serializer;

class FeatureCollectionDTO
{
    /**
     * @Serializer\Type("string")
     */
    private $type;

    /**
     * @Serializer\Type("array<App\DTO\Feature\FeatureDTO>")
     */
    private $features;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }
}
