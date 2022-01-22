<?php

namespace App\Modules\Ares\Domain\Specification\Player;

use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Gaia\Model\Place;

class CanRecycle extends PlayerSpecification
{
	/**
	 * @param Place $candidate
	 */
	public function isSatisfiedBy($candidate): bool
	{
		return \in_array($candidate->sectorColor, [$this->player->rColor, ColorResource::NO_FACTION]);
	}
}
