<?php

namespace App\Entity;

use App\DTO\Lantern\LanternDTO;
use App\Repository\LanternRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\SerializerBuilder;

/**
 * @ORM\Entity(repositoryClass=LanternRepository::class)
 */
class Lantern
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
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $lightFlow;

    /**
     * @ORM\ManyToMany(targetEntity=Map::class, mappedBy="lanterns")
     */
    private $maps;

    /**
     * @ORM\Column(type="text")
     */
    private $isolux;

    /**
     * @ORM\ManyToOne(targetEntity=LanternType::class, inversedBy="lanterns")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Curve::class, inversedBy="lanterns")
     * @ORM\JoinColumn(nullable=false)
     */
    private $curve;

    public function __construct()
    {
        $this->maps = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public static function fromDTO(LanternDTO $lanternDTO, EntityManager $em): self
    {
        $lantern = new self();
        $lantern->setName($lanternDTO->getName());

        $idLanternType = $em->getRepository(LanternType::class)
            ->findOneBy(['name' => $lanternDTO->getType()]
            );
        $lantern->setType($idLanternType);

        $lantern->setPrice($lanternDTO->getPrice());

        $idCurve = $em->getRepository(Curve::class)
            ->findOneBy(['name' => $lanternDTO->getCurve()]
            );
        $lantern->setCurve($idCurve);

        $lantern->setLightFlow($lanternDTO->getLightFlow());

        $serializer = SerializerBuilder::create()->build();
        $isolux = $serializer->serialize($lanternDTO->getMinIlluminations(), 'json');
        $lantern->setIsolux($isolux);

        return $lantern;
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getLightFlow(): ?int
    {
        return $this->lightFlow;
    }

    public function setLightFlow(int $lightFlow): self
    {
        $this->lightFlow = $lightFlow;

        return $this;
    }

    /**
     * @return Collection|Map[]
     */
    public function getMaps(): Collection
    {
        return $this->maps;
    }

    public function addMap(Map $map): self
    {
        if (!$this->maps->contains($map)) {
            $this->maps[] = $map;
            $map->addLantern($this);
        }

        return $this;
    }

    public function removeMap(Map $map): self
    {
        if ($this->maps->removeElement($map)) {
            $map->removeLantern($this);
        }

        return $this;
    }

    public function getIsolux(): ?string
    {
        return $this->isolux;
    }

    public function setIsolux(string $isolux): self
    {
        $this->isolux = $isolux;

        return $this;
    }

    public function getType(): ?LanternType
    {
        return $this->type;
    }

    public function setType(?LanternType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCurve(): ?Curve
    {
        return $this->curve;
    }

    public function setCurve(?Curve $curve): self
    {
        $this->curve = $curve;

        return $this;
    }
}
