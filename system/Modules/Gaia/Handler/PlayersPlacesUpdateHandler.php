<?php

namespace Asylamba\Modules\Gaia\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Gaia\Manager\PlaceManager;
use Asylamba\Modules\Gaia\Message\PlayersPlacesUpdateMessage;
use Asylamba\Modules\Gaia\Model\Place;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PlayersPlacesUpdateHandler implements MessageHandlerInterface
{
	public function __construct(
		protected PlaceManager $placeManager,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(PlayersPlacesUpdateMessage $message): void
	{
		$places = $this->placeManager->getPlayerPlaces();
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
			$maxResources = ceil($place->population / Place::COEFFPOPRESOURCE) * Place::COEFFMAXRESOURCE * ($place->maxDanger + 1);
			foreach ($hours as $hour) {
				$place->resources += floor(Place::COEFFRESOURCE * $place->population);
			}
			$place->resources = abs($place->resources - $initialResources);
			if ($place->resources > $maxResources) {
				$place->resources = $maxResources;
			}
			$repository->updatePlace($place);
		}
		$this->entityManager->commit();
		$this->entityManager->clear(Place::class);
	}
}
