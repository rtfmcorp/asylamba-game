<?php

namespace App\Modules\Promethee\Message;

class TechnologyQueueMessage
{
	public function __construct(private int $technologyQueueId)
	{

	}

	public function getTechnologyQueueId(): int
	{
		return $this->technologyQueueId;
	}
}
