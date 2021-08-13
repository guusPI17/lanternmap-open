<?php

namespace App\Entity;

use App\Repository\CurveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CurveRepository::class)
 */
class Curve
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
     * @ORM\Column(type="text")
     */
    private $coefTable;

    /**
     * @ORM\OneToMany(targetEntity=Lantern::class, mappedBy="curve", orphanRemoval=true)
     */
    private $lanterns;

    public function __construct()
    {
        $this->lanterns = new ArrayCollection();
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

    public function getCoefTable(): ?string
    {
        return $this->coefTable;
    }

    public function setCoefTable(string $coefTable): self
    {
        $this->coefTable = $coefTable;

        return $this;
    }

    /**
     * @return Collection|Lantern[]
     */
    public function getLanterns(): Collection
    {
        return $this->lanterns;
    }

    public function addLantern(Lantern $lantern): self
    {
        if (!$this->lanterns->contains($lantern)) {
            $this->lanterns[] = $lantern;
            $lantern->setCurve($this);
        }

        return $this;
    }

    public function removeLantern(Lantern $lantern): self
    {
        if ($this->lanterns->removeElement($lantern)) {
            // set the owning side to null (unless already changed)
            if ($lantern->getCurve() === $this) {
                $lantern->setCurve(null);
            }
        }

        return $this;
    }
}
