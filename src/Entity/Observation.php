<?php

namespace App\Entity;

use App\Repository\ObservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObservationRepository::class)]
class Observation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $year = null;

    #[ORM\Column(type: 'integer')]
    private ?int $month = null;

    #[ORM\ManyToOne(targetEntity: Indicator::class, inversedBy: 'observations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?\App\Entity\Indicator $indicator = null;

    #[ORM\Column(type: 'float')]
    private ?float $value = null;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getIndicator(): ?Indicator
    {
        return $this->indicator;
    }

    public function setIndicator(?Indicator $observation): self
    {
        $this->indicator = $observation;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function fill(Observation $observation)
    {
        $this->id = $observation->getId();
        $this->year = $observation->getYear();
        $this->month = $observation->getMonth();
        $this->indicator = $observation->getIndicator();
        $this->value = $observation->getValue();
        $this->notes = $observation->getNotes();
    }

}
