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
	) {

	}

	public function onServerInit(ServerInitEvent $event): void
	{
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
