<?php

namespace Asylamba\Modules\Demeter\Message;

class SenateUpdateMessage
{
	public function __construct(protected int $factionId)
	{

	}

	public function getFactionId(): int
	{
		return $this->factionId;
	}
}