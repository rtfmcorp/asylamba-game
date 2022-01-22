<?php

namespace App\Modules\Ares\Domain\Specification\Player;

use App\Modules\Athena\Model\OrbitalBase;
use App\Shared\Domain\Specification\Specification;

abstract class OrbitalBaseSpecification implements Specification
{
	public function __construct(protected OrbitalBase $orbitalBase)
	{

	}
}
