<?php

namespace App\Entity;

use App\Repository\VideojuegoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideojuegoRepository::class)]
class Videojuego
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'videojuegos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255)]
    private ?string $url_foto = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precio = null;

    /**
     * @var Collection<int, Clave>
     */
    #[ORM\OneToMany(targetEntity: Clave::class, mappedBy: 'videojuego', orphanRemoval: true)]
    private Collection $claves;

    public function __construct()
    {
        $this->claves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getUrlFoto(): ?string
    {
        return $this->url_foto;
    }

    public function setUrlFoto(string $url_foto): static
    {
        $this->url_foto = $url_foto;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * @return Collection<int, Clave>
     */
    public function getClaves(): Collection
    {
        return $this->claves;
    }

    public function addClave(Clave $clave): static
    {
        if (!$this->claves->contains($clave)) {
            $this->claves->add($clave);
            $clave->setVideojuego($this);
        }

        return $this;
    }

    public function removeClave(Clave $clave): static
    {
        if ($this->claves->removeElement($clave)) {
            // set the owning side to null (unless already changed)
            if ($clave->getVideojuego() === $this) {
                $clave->setVideojuego(null);
            }
        }

        return $this;
    }
}
