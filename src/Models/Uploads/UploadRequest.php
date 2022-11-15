<?php

namespace App\Models\Uploads;

use Yabx\RestBundle\Attributes\RestRequest;
use Yabx\TypeScriptBundle\Attributes\Definition;
use Symfony\Component\Validator\Constraints as Assert;

#[RestRequest]
class UploadRequest {

    #[Assert\NotBlank]
    #[Definition('{ name: string, data: string }')]
    public array $upload;

    public bool $extra = false;

    public ?int $resize = null;

    public bool $lock = false;

}
