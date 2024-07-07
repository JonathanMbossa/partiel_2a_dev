<?php

// src/Entity/Bulletin.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\BulletinRepository")]
class Bulletin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "array")]
    private array $choix = [];

    #[ORM\ManyToOne(targetEntity: "App\Entity\Election", inversedBy: "bulletins")]
    #[ORM\JoinColumn(nullable: false)]
    private Election $election;

    public function getId(): int
    {
        return $this->id;
    }

    public function getChoix(): array
    {
        return $this->choix;
    }

    public function setChoix(array $choix): self
    {
        $this->choix = $choix;
        return $this;
    }

    public function getElection(): Election
    {
        return $this->election;
    }

    public function setElection(Election $election): self
    {
        $this->election = $election;
        return $this;
    }
}
