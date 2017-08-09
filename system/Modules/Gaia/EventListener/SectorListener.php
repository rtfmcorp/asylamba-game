<?php

namespace Asylamba\Modules\Gaia\EventListener;

use Asylamba\Modules\Gaia\Manager\SectorManager;
use Asylamba\Modules\Gaia\Manager\SystemManager;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Gaia\Event\PlaceOwnerChangeEvent;

class SectorListener
{
	/** @var SectorManager **/
	protected $sectorManager;
	/** @var SystemManager **/
	protected $systemManager;
	/** @var EntityManager **/
	protected $entityManager;
	/** @var int **/
	protected $minimalScore;
	
	/**
	 * @param SectorManager $sectorManager
	 * @param SystemManager $systemManager
	 * @param EntityManager $entityManager
     * @param RedisManager $redisManager
	 * @param array $scores
	 * @param int $minimalScore
	 */
	public function __construct(
		SectorManager $sectorManager,
		SystemManager $systemManager,
		EntityManager $entityManager,
		$minimalScore
	)
	{
		$this->sectorManager = $sectorManager;
		$this->systemManager = $systemManager;
		$this->entityManager = $entityManager;
		$this->minimalScore = $minimalScore;
	}
	
	/**
	 * @param PlaceOwnerChangeEvent $event
	 */
	public function onPlaceOwnerChange(PlaceOwnerChangeEvent $event)
	{
		$system = $this->systemManager->get($event->getPlace()->rSystem);
		$sector = $this->sectorManager->get($system->rSector);
        $scores = $this->sectorManager->calculateOwnership($sector);
        
		$newColor = key($scores);
		$hasEnoughPoints = false;
		foreach ($scores as $factionId => $score) {
			if ($factionId !== 0 && $score >= $this->minimalScore) {
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
