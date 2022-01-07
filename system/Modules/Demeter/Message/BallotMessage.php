<?php

namespace Asylamba\Modules\Demeter\Message;

class BallotMessage
{
	public function __construct(protected int $factionId)
	{

	}

	public function getFactionId(): int
	{
		return $this->factionId;
	}
}
