<?php

namespace App\Entity;

use App\Repository\CaMonthRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaMonthRepository::class)]
class CaMonth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $rising = null;

    #[ORM\Column]
    private ?string $month = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRising(): ?string
    {
        return $this->rising;
    }

    public function setRising(string $rising): static
    {
        $this->rising = $rising;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(?string $month): static
    {
        $this->month = $month;

        return $this;
    }
}
