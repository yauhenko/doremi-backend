<?php

namespace App\Enum;

enum MonetizationStatus: string {
    case Pending = 'pending';
    case Active = 'active';
    case Blocked = 'blocked';
}
