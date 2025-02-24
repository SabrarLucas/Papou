<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DetailRepository;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DetailRepository::class)]
#[ApiResource]
class Detail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $price_tot = null;

    #[ORM\ManyToOne(inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Product $id_product = null;

    #[ORM\ManyToOne(inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $id_order = null;

    #[ORM\Column(length: 100)]
    #[Groups(["getIdOrder"])]
    private ?string $name_product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceTot(): ?string
    {
        return $this->price_tot;
    }

    public function setPriceTot(string $price_tot): static
    {
        $this->price_tot = $price_tot;

        return $this;
    }

    public function getIdProduct(): ?Product
    {
        return $this->id_product;
    }

    public function setIdProduct(?Product $id_product): static
    {
        $this->id_product = $id_product;

        return $this;
    }

    public function getIdOrder(): ?Order
    {
        return $this->id_order;
    }

    public function setIdOrder(?Order $id_order): static
    {
        $this->id_order = $id_order;

        return $this;
    }

    public function getNameProduct(): ?string
    {
        return $this->name_product;
    }

    public function setNameProduct(string $name_product): static
    {
        $this->name_product = $name_product;

        return $this;
    }
}
