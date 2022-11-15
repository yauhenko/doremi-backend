<?php

namespace App\Models\Users;

use Yabx\RestBundle\Attributes\Name;
use Yabx\RestBundle\Attributes\RestRequest;
use Symfony\Component\Validator\Constraints as Assert;

#[RestRequest]
class AuthRequest {

    #[Name('E-mail')]
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Name('Пароль')]
    #[Assert\NotBlank]
    public string $password;

}
