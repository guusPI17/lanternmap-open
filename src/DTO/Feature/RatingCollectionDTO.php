<?php

namespace App\DTO\Feature;

use JMS\Serializer\Annotation as Serializer;

class RatingCollectionDTO
{
    /**
     * @Serializer\Type("array<App\DTO\Feature\FeatureRatingDTO>")
     */
    private $features;

    public function getFeatures(): ?array
    {
        return $this->features;
    }

    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }
}
