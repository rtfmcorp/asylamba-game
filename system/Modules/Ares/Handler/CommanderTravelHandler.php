<?php

namespace Asylamba\Modules\Ares\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Ares\Manager\CommanderManager;
use Asylamba\Modules\Ares\Manager\ConquestManager;
use Asylamba\Modules\Ares\Manager\LootManager;
use Asylamba\Modules\Ares\Message\CommanderTravelMessage;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Gaia\Manager\PlaceManager;
use Asylamba\Modules\Gaia\Model\Place;
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
