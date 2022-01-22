<?php

namespace App\Modules\Ares\Domain\Specification\Player;

use App\Modules\Gaia\Model\Place;

class CanSpyPlace extends PlayerSpecification
{
	/**
	 * @param Place $candidate
	 */
	public function isSatisfiedBy($candidate): bool
	{
		return ($candidate->rPlayer != 0 && $candidate->playerColor != $this->player->rColor) || ($candidate->rPlayer == 0 && $candidate->typeOfPlace == 1);
	}
}
