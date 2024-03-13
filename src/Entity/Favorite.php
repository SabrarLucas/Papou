<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\FavoriteRepository;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[ApiResource]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: true,  onDelete: 'CASCADE')]
    private ?Product $id_product = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?User $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}
