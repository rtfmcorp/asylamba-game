<?php

namespace App\Modules\Ares\Message;

class CommanderTravelMessage
{
	public function __construct(protected int $commanderId)
	{

	}

	public function getCommanderId(): int
	{
		return $this->commanderId;
	}
}
