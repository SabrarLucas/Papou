<?php

namespace App\Entity;

use App\Repository\CaDaysRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaDaysRepository::class)]
class CaDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $rising = null;

    #[ORM\Column]
    private ?int $day = null;

    #[ORM\Column]
    private ?int $month = null;

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

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): static
    {
        $this->month = $month;

        return $this;
    }
}
