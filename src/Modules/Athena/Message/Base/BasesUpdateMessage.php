<?php

namespace App\Modules\Athena\Message\Base;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BasesUpdateMessage implements MessageHandlerInterface
{
	public function __invoke(BasesUpdateMessage $message): void
	{

	}
}
