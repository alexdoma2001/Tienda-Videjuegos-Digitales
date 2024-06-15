<?php

namespace App\Entity;

use App\Repository\ClaveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClaveRepository::class)]
class Clave
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $codigo = null;

    #[ORM\ManyToOne(inversedBy: 'claves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Videojuego $videojuego = null;

    #[ORM\ManyToOne(inversedBy: 'claves')]
    private ?Pedido $pedido = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getVideojuego(): ?Videojuego
    {
        return $this->videojuego;
    }

    public function setVideojuego(?Videojuego $videojuego): static
    {
        $this->videojuego = $videojuego;

        return $this;
    }

    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    public function setPedido(?Pedido $pedido): static
    {
        $this->pedido = $pedido;

        return $this;
    }

    public function Disponibilidad(): bool
    {
        return $this->pedido === null;
    }
}
