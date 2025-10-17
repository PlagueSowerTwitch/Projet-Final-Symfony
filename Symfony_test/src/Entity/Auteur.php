<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuteurRepository::class)]
class Auteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 75)]
    private ?string $Nom = null;

    #[ORM\Column(length: 75)]
    private ?string $Prenom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Biographie = null;

    #[ORM\Column]
    private ?\DateTime $DateNaissance = null;

    /**
     * @var Collection<int, Livre>
     */
    #[ORM\OneToMany(targetEntity: Livre::class, mappedBy: 'idAuteur')]
    private Collection $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->Biographie;
    }

    public function setBiographie(string $Biographie): static
    {
        $this->Biographie = $Biographie;

        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->DateNaissance;
    }

    public function setDateNaissance(\DateTime $DateNaissance): static
    {
        $this->DateNaissance = $DateNaissance;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): static
    {
        if (!$this->livres->contains($livre)) {
            $this->livres->add($livre);
            $livre->setIdAuteur($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getIdAuteur() === $this) {
                $livre->setIdAuteur(null);
            }
        }

        return $this;
    }
}
