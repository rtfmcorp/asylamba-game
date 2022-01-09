<?php

namespace App\Modules\Athena\Message\Building;

class BuildingQueueMessage
{
	public function __construct(private int $buildingQueueId)
	{
	}

	public function getBuildingQueueId(): int
	{
		return $this->buildingQueueId;
	}
}
