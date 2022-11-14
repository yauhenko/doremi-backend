<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\GeneratedValue;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface {

    #[Id]
    #[GeneratedValue]
    #[Column]
    #[Groups(['main'])]
    private int $id;

    #[Column(length: 180, unique: true)]
    #[Groups(['main'])]
    private string $email;

    #[Column]
    private array $roles = [];

    #[Column]
    private string $password;

    #[Column]
    private DateTimeImmutable $createdAt;

    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int {
        return $this->id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string {
        return $this->email;
    }

    public function getRoles(): array {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials() {
        // nothing to do
    }

}
