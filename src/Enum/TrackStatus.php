<?php

namespace App\Enum;

enum TrackStatus: string {
    case Draft = 'draft';
    case Review = 'review';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
