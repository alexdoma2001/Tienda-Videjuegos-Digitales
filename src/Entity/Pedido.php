<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
class Pedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha_realizado = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precio_final = null;

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    /**
     * @var Collection<int, Clave>
     */
    #[ORM\OneToMany(targetEntity: Clave::class, mappedBy: 'pedido')]
    private Collection $claves;

    public function __construct()
    {
        $this->claves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaRealizado(): ?\DateTimeInterface
    {
        return $this->fecha_realizado;
    }

    public function setFechaRealizado(\DateTimeInterface $fecha_realizado): static
    {
        $this->fecha_realizado = $fecha_realizado;

        return $this;
    }

    public function getPrecioFinal(): ?string
    {
        return $this->precio_final;
    }

    public function setPrecioFinal(string $precio_final): static
    {
        $this->precio_final = $precio_final;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection<int, Clave>
     */
    public function getClaves(): Collection
    {
        return $this->claves;
    }

    public function añadirClave(Clave $clave): static
    {
        if (!$this->claves->contains($clave)) {
            $this->claves->add($clave);
            $clave->setPedido($this);
        }

        return $this;
    }

    public function eliminarClave(Clave $clave): static
    {
        if ($this->claves->removeElement($clave)) {
            if ($clave->getPedido() === $this) {
                $clave->setPedido(null);
            }
        }

        return $this;
    }
}
