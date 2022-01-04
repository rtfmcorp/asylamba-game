<?php

namespace Asylamba\Modules\Gaia\EventListener;

use Asylamba\Modules\Gaia\Manager\SystemManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Gaia\Event\PlaceOwnerChangeEvent;

class SystemListener
{
	public function __construct(
		protected SystemManager $systemManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected PlayerManager $playerManager,
		protected EntityManager $entityManager,
		protected array $scores
	) {
	}
	
	/**
	 * @param PlaceOwnerChangeEvent $event
	 */
	public function onPlaceOwnerChange(PlaceOwnerChangeEvent $event)
	{
		$system = $this->systemManager->get($event->getPlace()->rSystem);
		$bases = $this->orbitalBaseManager->getSystemBases($system);
		// Initialize the value in case no base is available (after leaving the last one)
		$scores[$system->rColor] = 0;
		
		foreach ($bases as $base)
		{
			$player = $this->playerManager->get($base->rPlayer);
			
			$scores[$player->rColor] =
				(!empty($scores[$player->rColor]))
				? $scores[$player->rColor] + $this->scores[$base->typeOfBase]
				: $this->scores[$base->typeOfBase]
			;
		}
		arsort($scores);
		reset($scores);
		$newColor = key($scores);
		// NPC faction has no points
		$scores[0] = 0;
		if ($scores[$newColor] > 0 && $system->rColor !== $newColor && $scores[$newColor] > $scores[$system->rColor]) {
			$system->rColor = $newColor;
		} elseif ($scores[$newColor] === 0) {
			$system->rColor = 0;
		}
		$this->systemManager->changeOwnership($system);
	}
}
