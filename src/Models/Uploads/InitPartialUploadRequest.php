<?php

namespace App\Models\Uploads;

use Yabx\RestBundle\Attributes\RestRequest;
use Symfony\Component\Validator\Constraints\NotBlank;

#[RestRequest]
class InitPartialUploadRequest {

    #[NotBlank]
    public int $chunkSize;

    #[NotBlank]
    public int $fileSize;

    #[NotBlank]
    public string $fileName;

    public string $fileType;

    public bool $extra = false;

    public ?int $resize = null;

    public bool $lock = false;

}
