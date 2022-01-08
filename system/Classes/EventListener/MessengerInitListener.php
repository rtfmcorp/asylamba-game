<?php

namespace Asylamba\Classes\EventListener;

use Asylamba\Classes\Event\ServerInitEvent;
use Asylamba\Modules\Ares\Manager\CommanderManager;
use Asylamba\Modules\Athena\Manager\BuildingQueueManager;
use Asylamba\Modules\Athena\Manager\CommercialShippingManager;
use Asylamba\Modules\Athena\Manager\RecyclingMissionManager;
use Asylamba\Modules\Athena\Manager\ShipQueueManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Promethee\Manager\TechnologyQueueManager;
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
