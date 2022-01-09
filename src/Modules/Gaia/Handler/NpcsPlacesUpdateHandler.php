<?php

namespace App\Modules\Gaia\Handler;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Utils;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Gaia\Message\NpcsPlacesUpdateMessage;
use App\Modules\Gaia\Model\Place;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NpcsPlacesUpdateHandler implements MessageHandlerInterface
{
	public function __construct(
		protected PlaceManager $placeManager,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(NpcsPlacesUpdateMessage $message): void
	{
		$places = $this->placeManager->getNpcPlaces();
		$now   = Utils::now();
		$repository = $this->entityManager->getRepository(Place::class);
		$this->entityManager->beginTransaction();

		foreach ($places as $place) {
			if (Utils::interval($place->uPlace, $now, 's') === 0) {
				continue;
			}
			# update time
			$hours = Utils::intervalDates($now, $place->uPlace);
			$place->uPlace = $now;
			$initialResources = $place->resources;
			$initialDanger = $place->danger;
			$maxResources = ceil($place->population / Place::COEFFPOPRESOURCE) * Place::COEFFMAXRESOURCE * ($place->maxDanger + 1);

			foreach ($hours as $hour) {
				$place->danger += Place::REPOPDANGER;
				$place->resources += floor(Place::COEFFRESOURCE * $place->population);
			}
			// The repository method will add the new resources. We have to calculate how many resources have been added
			$place->resources = abs($place->resources - $initialResources);
			// If the max is reached, we have to add just the difference between the max and init value
			if ($place->resources > $maxResources) {
				$place->resources = $maxResources - $initialResources;
			}
			$place->danger = abs($place->danger - $initialDanger);
			// Same thing here
			if ($place->danger > $place->maxDanger) {
				$place->danger = $place->maxDanger - $initialDanger;
			}
			$repository->updatePlace($place, true);
		}
		$repository->npcQuickfix();
		$this->entityManager->commit();
		$this->entityManager->clear(Place::class);
	}
}
