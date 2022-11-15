<?php

namespace App\Entity;

use App\Utils\Env;
use DateTimeInterface;
use DateTimeImmutable;
use App\Utils\DBAL\Options;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use App\Repository\UploadRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity(repositoryClass: UploadRepository::class)]
class Upload {

    #[Id]
    #[Column(length: 32, options: [Options::FIXED => true])]
    #[Groups(['main'])]
    private string $id;

    #[Column(length: 255)]
    private string $path;

    #[Column(length: 255)]
    #[Groups(['upload:name', 'upload:full'])]
    private string $name;

    #[Column(length: 50)]
    #[Groups(['upload:mime', 'upload:full'])]
    private string $mime;

    #[Column]
    #[Groups(['upload:size', 'upload:full'])]
    private int $size;

    #[Column]
    private DateTimeImmutable $createdAt;

    #[Column]
    private DateTimeImmutable $touchedAt;

    #[Column]
    private bool $isLocked;

    public function __construct(string $id, string $path, string $name, string $mime, int $size, bool $isLocked = false) {
        $this->id = $id;
        $this->path = $path;
        $this->name = $name;
        $this->mime = $mime;
        $this->size = $size;
        $this->isLocked = $isLocked;
        $this->createdAt = new DateTimeImmutable;
        $this->touch();
    }

    public function touch(): self {
        $this->touchedAt = new DateTimeImmutable();
        return $this;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getFullPath(): string {
        $path = __DIR__ . '/../../public' . $this->path;
        return realpath($path) ?: $path;
    }

    #[Groups(['main'])]
    public function getUrl(): string {
        return Env::get('API_URL') . $this->path;
    }

    public function getTouchedAt(): DateTimeInterface {
        return $this->touchedAt;
    }

    public function getIsLocked(): bool {
        return $this->isLocked;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getMime(): string {
        return $this->mime;
    }

    public function getSize(): int {
        return $this->size;
    }

}
