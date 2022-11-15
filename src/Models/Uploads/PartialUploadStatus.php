<?php

namespace App\Models\Uploads;

use App\Entity\Upload;
use App\Utils\Password;
use App\Service\Uploader;
use Symfony\Component\Serializer\Annotation\Groups;

class PartialUploadStatus {

    #[Groups(['main'])]
    protected string $id;

    protected string $fileName;

    protected string $fileType;

    protected int $fileSize;

    protected int $chunkSize;

    protected int $uploaded = 0;

    #[Groups(['main'])]
    protected int $progress = 0;

    #[Groups(['main'])]
    protected ?Upload $upload = null;

    protected InitPartialUploadRequest $request;

    public function __construct(InitPartialUploadRequest $request) {
        $this->id = Uploader::id();
        $this->fileName = $request->fileName;
        $this->fileType = $request->fileType ?: 'application/octet-stream';
        $this->fileSize = $request->fileSize;
        $this->chunkSize = $request->chunkSize;
        $this->request = $request;
    }

    public function processChunk(PartialChunkUploadRequest $request): void {
        $chunk = base64_decode($request->chunk);
        $f = fopen("/tmp/{$this->id}", "a");
        fwrite($f, $chunk);
        fclose($f);
        $this->uploaded += strlen($chunk);
        $this->progress = round($this->uploaded / $this->fileSize * 100, 2);
    }

    public function getId(): string {
        return $this->id;
    }

    public function getFileName(): string {
        return $this->fileName;
    }

    public function getFileType(): string {
        return $this->fileType;
    }

    public function getFileSize(): int {
        return $this->fileSize;
    }

    public function getChunkSize(): int {
        return $this->chunkSize;
    }

    public function getUploaded(): int {
        return $this->uploaded;
    }

    public function getProgress(): int {
        return $this->progress;
    }

    public function getUpload(): ?Upload {
        return $this->upload;
    }

    public function setUpload(Upload $upload): void {
        $this->upload = $upload;
    }

    public function getRequest(): InitPartialUploadRequest {
        return $this->request;
    }

    #[Groups(['main'])]
    public function isReady(): bool {
        return $this->uploaded === $this->fileSize;
    }

}
