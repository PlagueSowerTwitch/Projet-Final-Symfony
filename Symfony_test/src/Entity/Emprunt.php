<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $DateEmprunt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $DateRendus = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $idUtilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livre $idLivre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEmprunt(): ?\DateTime
    {
        return $this->DateEmprunt;
    }

    public function setDateEmprunt(\DateTime $DateEmprunt): static
    {
        $this->DateEmprunt = $DateEmprunt;

        return $this;
    }

    public function getDateRendus(): ?\DateTime
    {
        return $this->DateRendus;
    }

    public function setDateRendus(?\DateTime $DateRendus): static
    {
        $this->DateRendus = $DateRendus;

        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateur
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?Utilisateur $idUtilisateur): static
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    public function getIdLivre(): ?Livre
    {
        return $this->idLivre;
    }

    public function setIdLivre(?Livre $idLivre): static
    {
        $this->idLivre = $idLivre;

        return $this;
    }
}
