<?php

namespace App\Entity;

use App\Repository\LocalityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocalityRepository::class)
 */
class Locality
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $dataMovement;

    /**
     * @ORM\OneToMany(targetEntity=Map::class, mappedBy="locality", orphanRemoval=true)
     */
    private $maps;

    /**
     * @ORM\Column(type="array")
     */
    private $latitude = [];

    /**
     * @ORM\Column(type="array")
     */
    private $longitude = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $timezone;

    public function __construct()
    {
        $this->maps = new ArrayCollection();
    }

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

    public function getDataMovement(): ?string
    {
        return $this->dataMovement;
    }

    public function setDataMovement(?string $dataMovement): self
    {
        $this->dataMovement = $dataMovement;

        return $this;
    }

    /**
     * @return Collection|Map[]
     */
    public function getMap(): Collection
    {
        return $this->maps;
    }

    public function addMap(Map $map): self
    {
        if (!$this->maps->contains($map)) {
            $this->maps[] = $map;
            $map->setLocality($this);
        }

        return $this;
    }

    public function removeMap(Map $map): self
    {
        if ($this->maps->removeElement($map)) {
            // set the owning side to null (unless already changed)
            if ($map->getLocality() === $this) {
                $map->setLocality(null);
            }
        }

        return $this;
    }

    public function getLatitude(): ?array
    {
        return $this->latitude;
    }

    public function setLatitude(array $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?array
    {
        return $this->longitude;
    }

    public function setLongitude(array $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }
}
