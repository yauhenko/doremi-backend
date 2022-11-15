<?php

namespace App\Models\Uploads;

use Yabx\RestBundle\Attributes\RestRequest;
use Symfony\Component\Validator\Constraints\NotBlank;

#[RestRequest]
class PartialChunkUploadRequest {

    #[NotBlank]
    public string $id;

    #[NotBlank]
    public string $chunk;

}
