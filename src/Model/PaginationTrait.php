<?php

namespace App\Model;

use Yabx\RestBundle\Attributes\Name;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

trait PaginationTrait {

    #[Name('Page')]
    #[GreaterThanOrEqual(1)]
    public int $page = 1;

    #[Name('Limit')]
    #[GreaterThanOrEqual(1)]
    #[LessThanOrEqual(1000)]
    public int $limit = 20;

}
