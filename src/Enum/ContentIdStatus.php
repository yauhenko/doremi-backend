<?php

namespace App\Enum;

enum ContentIdStatus: string {
    case NoNeed = 'no-need';    // Content ID не требуется
    case Queued = 'queued';     // В очереди на регистрацию
    case Pending = 'pending';   // Заявка подана, ожидает регистрации
    case Approved = 'approved'; // Зарегистрировано
    case Rejected = 'rejected'; // Что-то пошло не так
}
