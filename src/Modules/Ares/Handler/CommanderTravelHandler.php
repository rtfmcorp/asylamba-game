<?php

namespace App\Modules\Ares\Handler;

use App\Classes\Entity\EntityManager;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Manager\ConquestManager;
use App\Modules\Ares\Manager\LootManager;
use App\Modules\Ares\Message\CommanderTravelMessage;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Gaia\Model\Place;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommanderTravelHandler implements MessageHandlerInterface
{
	public function __construct(
		protected CommanderManager $commanderManager,
		protected ConquestManager $conquestManager,
		protected LootManager $lootManager,
		protected PlaceManager $placeManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(CommanderTravelMessage $commanderTravelMessage): void
	{
		$commander = $this->commanderManager->get($commanderTravelMessage->getCommanderId());

		match($commander->getTravelType()) {
			Commander::MOVE => $this->commanderManager->uChangeBase($commander),
			Commander::LOOT => $this->lootManager->loot($commander),
			Commander::COLO => $this->conquestManager->conquer($commander),
			Commander::BACK => $this->moveBack($commander),
		};
	}

	protected function moveBack(Commander $commander): void
	{
		$place = $this->placeManager->get($commander->rDestinationPlace);
		$commanderBase = $this->orbitalBaseManager->get($commander->rBase);

		$this->commanderManager->endTravel($commander, Commander::AFFECTED);

		$this->placeManager->sendNotif($place, Place::COMEBACK, $commander);

		if ($commander->resources > 0) {
			$this->orbitalBaseManager->increaseResources($commanderBase, $commander->resources, TRUE);
			$commander->resources = 0;
		}
		$this->entityManager->flush($commander);
	}
}
