<?php

namespace App\Enum;

enum UserRole: string {

    case Admin = 'admin';
    case Author = 'author';
    case Owner = 'owner';

    public function getRoles(): array {
        return ['ROLE_USER', 'ROLE_' . strtoupper($this->value)];
    }

}
