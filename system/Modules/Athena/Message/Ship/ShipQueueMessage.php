<?php

namespace Asylamba\Modules\Athena\Message\Ship;

class ShipQueueMessage
{
	public function __construct(protected int $shipQueueId)
	{

	}

	public function getShipQueueId(): int
	{
		return $this->shipQueueId;
	}
}
