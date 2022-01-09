<?php

namespace App\Modules\Gaia\EventListener;

use App\Classes\Redis\RedisManager;
use App\Modules\Gaia\Manager\SectorManager;
use App\Modules\Gaia\Manager\SystemManager;
use App\Classes\Entity\EntityManager;

use App\Modules\Gaia\Event\PlaceOwnerChangeEvent;

class SectorListener
{
	public function __construct(
		protected SectorManager $sectorManager,
		protected SystemManager $systemManager,
		protected EntityManager $entityManager,
		protected RedisManager $redisManager,
		protected array $scores,
		protected int $sectorMinimalScore,
	) {
	}

	public function onPlaceOwnerChange(PlaceOwnerChangeEvent $event): void
	{
		$system = $this->systemManager->get($event->getPlace()->rSystem);
		$sector = $this->sectorManager->get($system->rSector);
        $scores = $this->sectorManager->calculateOwnership($sector);
        
		$newColor = key($scores);
		$hasEnoughPoints = false;
		foreach ($scores as $factionId => $score) {
			if ($factionId !== 0 && $score >= $this->sectorMinimalScore) {
				$hasEnoughPoints = true;
				break;
			}
		}
		// If the faction has more points than the minimal score and the current owner of the sector, he claims it
		if ($hasEnoughPoints === true && $sector->rColor !== $newColor && $scores[$newColor] > $scores[$sector->rColor]) {
			$sector->rColor = $newColor;
		// If this is a prime sector, we do not pull back the color from the sector
		} elseif ($hasEnoughPoints === false && $sector->getPrime() === false) {
			$sector->rColor = 0;
		}
		$this->sectorManager->changeOwnership($sector);
	}
}
