<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
class Categoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, Videojuego>
     */
    #[ORM\OneToMany(targetEntity: Videojuego::class, mappedBy: 'categoria', orphanRemoval: false)]
    private Collection $videojuegos;

    public function __construct()
    {
        $this->videojuegos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Videojuego>
     */
    public function getVideojuegos(): Collection
    {
        return $this->videojuegos;
    }

    public function addVideojuego(Videojuego $videojuego): static
    {
        if (!$this->videojuegos->contains($videojuego)) {
            $this->videojuegos->add($videojuego);
            $videojuego->setCategoria($this);
        }

        return $this;
    }

    public function removeVideojuego(Videojuego $videojuego): static
    {
        if ($this->videojuegos->removeElement($videojuego)) {
            // set the owning side to null (unless already changed)
            if ($videojuego->getCategoria() === $this) {
                $videojuego->setCategoria(null);
            }
        }

        return $this;
    }
}
