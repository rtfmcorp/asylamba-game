<?php

namespace Asylamba\Modules\Athena\Handler\Ship;

use Asylamba\Modules\Athena\Message\Ship\ShipQueueMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ShipQueueHandler implements MessageHandlerInterface
{
	public function __invoke(ShipQueueMessage $message): void
	{

	}
}
