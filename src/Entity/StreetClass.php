<?php

namespace App\Entity;

use App\Repository\StreetClassRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StreetClassRepository::class)
 */
class StreetClass
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $averageIllumination;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAverageIllumination(): ?float
    {
        return $this->averageIllumination;
    }

    public function setAverageIllumination(float $averageIllumination): self
    {
        $this->averageIllumination = $averageIllumination;

        return $this;
    }
}
