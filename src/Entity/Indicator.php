<?php

namespace App\Entity;

use App\Repository\IndicatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IndicatorRepository::class)
 */
class Indicator
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $descriptionEs;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $descriptionEu;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $requiredRoles = [];

    /**
     * @ORM\OneToMany(targetEntity=Observation::class, mappedBy="indicator")
     */
    private $observations;

    public function __construct()
    {
        $this->observations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDescriptionEs(): ?string
    {
        return $this->descriptionEs;
    }

    public function setDescriptionEs(string $descriptionEs): self
    {
        $this->descriptionEs = $descriptionEs;

        return $this;
    }

    public function getDescriptionEu(): ?string
    {
        return $this->descriptionEu;
    }

    public function setDescriptionEu(string $descriptionEu): self
    {
        $this->descriptionEu = $descriptionEu;

        return $this;
    }

    public function getRequiredRoles(): ?array
    {
        return $this->requiredRoles;
    }

    public function setRequiredRoles(?array $requiredRoles): self
    {
        $this->requiredRoles = $requiredRoles;

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->getDescriptionEu() . ' / ' . $this->getDescriptionEs();
    }

    public function fill(Indicator $indicator)
    {
        $this->id = $indicator->getId();
        $this->descriptionEs = $indicator->getDescriptionEs();
        $this->descriptionEu = $indicator->getDescriptionEu();
        $this->requiredRoles = $indicator->getRequiredRoles();
    }

    /**
     * @return Collection|Observation[]
     */
    public function getObservations(): Collection
    {
        return $this->observations;
    }

    public function addObservation(Observation $observation): self
    {
        if (!$this->observations->contains($observation)) {
            $this->observations[] = $observation;
            $observation->setIndicator($this);
        }

        return $this;
    }

    public function removeObservation(Observation $observation): self
    {
        if ($this->observations->removeElement($observation)) {
            // set the owning side to null (unless already changed)
            if ($observation->getIndicator() === $this) {
                $observation->setIndicator(null);
            }
        }

        return $this;
    }

}
