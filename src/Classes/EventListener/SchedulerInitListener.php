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

class SchedulerInitListener
{
	public function __construct(
		protected CommanderManager $commanderManager,
		protected BuildingQueueManager $buildingQueueManager,
		protected CommercialShippingManager $commercialShippingManager,
		protected RecyclingMissionManager $recyclingMissionManager,
		protected ShipQueueManager $shipQueueManager,
		protected TechnologyQueueManager $technologyQueueManager,
		protected ColorManager $factionManager,
		protected LoggerInterface $logger,
	) {

	}

	public function onServerInit(ServerInitEvent $event): void
	{
		return;

		$this->logger->info('Scheduling planned tasks');

		$this->commanderManager->scheduleMovements();
		$this->buildingQueueManager->scheduleActions();
		$this->commercialShippingManager->scheduleShippings();
		$this->recyclingMissionManager->scheduleMissions();
		$this->shipQueueManager->scheduleActions();
		$this->technologyQueueManager->scheduleQueues();
		$this->factionManager->scheduleSenateUpdate();
		$this->factionManager->scheduleCampaigns();
		$this->factionManager->scheduleElections();
		$this->factionManager->scheduleBallot();
	}
}
