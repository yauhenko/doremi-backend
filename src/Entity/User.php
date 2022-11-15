<?php

namespace App\Entity;

use DateTimeImmutable;
use App\Enum\UserRole;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['main'])]
    private int $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['main'])]
    private string $email;

    #[ORM\Column(length: 20, enumType: UserRole::class)]
    private UserRole $role;

    #[ORM\Column]
    private string $password = 'not-set';

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(string $email, UserRole $role) {
        $this->email = $email;
        $this->role = $role;
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
       return $this->role->getRoles();
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

    public function getRole(): UserRole {
        return $this->role;
    }

}
