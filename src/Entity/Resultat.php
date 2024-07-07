<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ResultatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultatRepository::class)]
#[ApiResource]
class Resultat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ORM\OneToOne(targetEntity: Bulletin::class, inversedBy: "resultat", cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bulletin $bulletin = null;

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $resultat_scrutin = null;

    #[ORM\Column(length: 100)]
    private ?string $winner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResultatScrutin(): ?string
    {
        return $this->resultat_scrutin;
    }

    public function setResultatScrutin(string $resultat_scrutin): static
    {
        $this->resultat_scrutin = $resultat_scrutin;

        return $this;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function setWinner(string $winner): static
    {
        $this->winner = $winner;

        return $this;
    }
}
