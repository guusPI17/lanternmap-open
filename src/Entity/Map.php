<?php

namespace App\Entity;

use App\Repository\MapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MapRepository::class)
 */
class Map
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $report;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="maps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userAccount;

    /**
     * @ORM\ManyToOne(targetEntity=Locality::class, inversedBy="maps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $locality;

    /**
     * @ORM\ManyToMany(targetEntity=Lantern::class, inversedBy="maps")
     */
    private $lanterns;

    public function __construct()
    {
        $this->lanterns = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
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

    public function getReport(): ?string
    {
        return $this->report;
    }

    public function setReport(?string $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function getUserAccount(): ?User
    {
        return $this->userAccount;
    }

    public function setUserAccount(?User $userAccount): self
    {
        $this->userAccount = $userAccount;

        return $this;
    }

    public function getLocality(): ?Locality
    {
        return $this->locality;
    }

    public function setLocality(?Locality $locality): self
    {
        $this->locality = $locality;

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
        }

        return $this;
    }

    public function removeLantern(Lantern $lantern): self
    {
        $this->lanterns->removeElement($lantern);

        return $this;
    }
}
