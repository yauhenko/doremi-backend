<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use App\Repository\AuthorRepository;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\GeneratedValue;

#[Entity(repositoryClass: AuthorRepository::class)]
class Author {

    #[Id]
    #[GeneratedValue]
    #[Column]
    private int $id;

    #[ManyToOne]
    #[JoinColumn(nullable: false)]
    private User $user;

    #[Column(length: 100)]
    private string $name = '';

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): self {
        $this->user = $user;
        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

}
