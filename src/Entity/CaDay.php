<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CaDayRepository;

#[ORM\Entity(repositoryClass: CaDayRepository::class)]
class CaDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $rising = null;

    #[ORM\Column]
    private ?string $day = null;

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

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }
}
