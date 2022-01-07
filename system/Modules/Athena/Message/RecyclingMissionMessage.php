<?php

namespace Asylamba\Modules\Athena\Message;

class RecyclingMissionMessage
{
	public function __construct(protected int $recyclingMissionId)
	{

	}

	public function getRecyclingMissionId(): int
	{
		return $this->recyclingMissionId;
	}
}
