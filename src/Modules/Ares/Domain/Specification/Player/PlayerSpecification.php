<?php

namespace App\Modules\Ares\Domain\Specification\Player;

use App\Modules\Zeus\Model\Player;
use App\Shared\Domain\Specification\Specification;

abstract class PlayerSpecification implements Specification
{
	public function __construct(protected Player $player)
	{

	}
}
