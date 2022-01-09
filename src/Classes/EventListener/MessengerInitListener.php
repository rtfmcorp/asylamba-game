<?php

namespace App\Classes\EventListener;

use App\Classes\Event\ServerInitEvent;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Athena\Manager\BuildingQueueManager;
use App\Modules\Athena\Manager\CommercialShippingManager;
use App\Modules\Athena\Manager\RecyclingMissionManager;
use App\Modules\Athena\Manager\ShipQueueManager;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Promethee\Manager\TechnologyQueueManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\Connection;

class MessengerInitListener
{
	public function __construct(
		protected Connection $amqpConnection,
		protected LoggerInterface $logger,
	) {

	}

	public function onServerInit(ServerInitEvent $event): void
	{
		$this->amqpConnection->setup();
		$this->amqpConnection->purgeQueues();

		$this->logger->info('AMQP queues have been setup');
	}
}
