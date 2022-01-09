<?php

namespace App\Modules\Demeter\Message;

class ElectionMessage
{
	public function __construct(protected int $factionId)
	{

	}

	public function getFactionId(): int
	{
		return $this->factionId;
	}
}
