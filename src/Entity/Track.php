<?php

namespace App\Entity;

use App\Enum\TrackStatus;
use App\Utils\DBAL\Options;
use App\Enum\ContentIdStatus;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrackRepository;

#[ORM\Entity(repositoryClass: TrackRepository::class)]
class Track {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 100)]
    private string $artist;

    #[ORM\ManyToOne]
    private ?Upload $file = null;

    #[ORM\ManyToOne]
    private ?Upload $cover = null;

    #[ORM\ManyToOne]
    private ?Author $author = null;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    #[ORM\Column]
    private ?string $lyrics;

    #[ORM\Column(length: 2, options: [Options::FIXED => true])]
    private ?string $language;

    #[ORM\Column(enumType: TrackStatus::class)]
    private TrackStatus $status = TrackStatus::Draft;

    #[ORM\Column(enumType: TrackStatus::class)]
    private ContentIdStatus $contentIdStatus = ContentIdStatus::NoNeed;

    public function getId(): int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getArtist(): ?string {
        return $this->artist;
    }

    public function setArtist(string $artist): self {
        $this->artist = $artist;
        return $this;
    }

    public function getFile(): ?Upload {
        return $this->file;
    }

    public function setFile(?Upload $file): self {
        $this->file = $file;
        return $this;
    }

    public function getCover(): ?Upload {
        return $this->cover;
    }

    public function setCover(?Upload $cover): self {
        $this->cover = $cover;
        return $this;
    }

    public function getAuthor(): ?Author {
        return $this->author;
    }

    public function setAuthor(?Author $author): void {
        $this->author = $author;
    }

    public function getOwner(): ?User {
        return $this->owner;
    }

    public function setOwner(?User $owner): void {
        $this->owner = $owner;
    }

    public function getStatus(): TrackStatus {
        return $this->status;
    }

    public function setStatus(TrackStatus $status): void {
        $this->status = $status;
    }

    public function getLyrics(): ?string {
        return $this->lyrics;
    }

    public function setLyrics(?string $lyrics): void {
        $this->lyrics = $lyrics;
    }

    public function getLanguage(): ?string {
        return $this->language;
    }

    public function setLanguage(string $language): void {
        $this->language = $language;
    }

    public function getContentIdStatus(): ContentIdStatus {
        return $this->contentIdStatus;
    }

    public function setContentIdStatus(ContentIdStatus $contentIdStatus): void {
        $this->contentIdStatus = $contentIdStatus;
    }

}
