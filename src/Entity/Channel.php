<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\MonetizationStatus;
use App\Repository\ChannelRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ChannelRepository::class)]
class Channel {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 100)]
    #[Groups(['main'])]
    private string $name;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['main'])]
    private string $url;

    #[ORM\ManyToOne]
    #[Groups(['main'])]
    private ?Upload $icon;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['channel:owner', 'channel:full'])]
    private User $owner;

    #[ORM\Column(enumType: MonetizationStatus::class)]
    public MonetizationStatus $monetizationStatus = MonetizationStatus::Pending;

    #[ORM\Column]
    #[Groups(['channel:full'])]
    private DateTimeImmutable $createdAt;

    public function __construct(User $owner, string $name, string $url, ?Upload $icon = null) {
        $this->owner = $owner;
        $this->name = $name;
        $this->url = $url;
        $this->icon = $icon;
        $this->createdAt = new DateTimeImmutable;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function setUrl(string $url): self {
        $this->url = $url;
        return $this;
    }

    public function getIcon(): ?Upload {
        return $this->icon;
    }

    public function setIcon(?Upload $icon): self {
        $this->icon = $icon;
        return $this;
    }

    public function getOwner(): ?User {
        return $this->owner;
    }

    public function setOwner(User $owner): self {
        $this->owner = $owner;
        return $this;
    }

    public function getMonetizationStatus(): MonetizationStatus {
        return $this->monetizationStatus;
    }

    public function setMonetizationStatus(MonetizationStatus $monetizationStatus): void {
        $this->monetizationStatus = $monetizationStatus;
    }

    public function getCreatedAt(): DateTimeImmutable {
        return $this->createdAt;
    }

}
