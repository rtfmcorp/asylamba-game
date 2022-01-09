<?php

namespace App\Modules\Demeter\Message;

class CampaignMessage
{
	public function __construct(protected int $factionId)
	{

	}

	public function getFactionId(): int
	{
		return $this->factionId;
	}
}
