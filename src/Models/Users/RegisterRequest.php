<?php

namespace App\Models\Users;

use App\Enum\UserRole;
use Yabx\RestBundle\Attributes\Name;
use Yabx\RestBundle\Validator\EnumChoice;
use Yabx\RestBundle\Attributes\RestRequest;
use Symfony\Component\Validator\Constraints as Assert;

#[RestRequest]
class RegisterRequest {

    #[Name('E-mail')]
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Name('Пароль')]
    #[Assert\NotBlank]
    public string $password;

    #[Name('Роль')]
    #[Assert\NotBlank]
    #[EnumChoice(UserRole::class)]
    public UserRole $role;

}
