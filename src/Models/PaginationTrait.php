<?php

namespace App\Models;

use Yabx\RestBundle\Attributes\Name;
use Symfony\Component\Validator\Constraints as Assert;

trait PaginationTrait {

	#[Name('Страница')]
	#[Assert\GreaterThanOrEqual(1)]
	public int $page = 1;

	#[Name('Лимит на страницу')]
	#[Assert\GreaterThanOrEqual(1)]
	#[Assert\LessThanOrEqual(1000)]
	public int $limit = 20;

}
