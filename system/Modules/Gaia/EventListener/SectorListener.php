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
	/** @var OrbitalBaseManager **/
	protected $orbitalBaseManager;
	/** @var PlayerManager **/
	protected $playerManager;
	/** @var EntityManager **/
	protected $entityManager;
	/** @var array **/
	protected $scores;
	/** @var int **/
	protected $minimalScore;
	
	/**
	 * @param SectorManager $sectorManager
	 * @param SystemManager $systemManager
	 * @param OrbitalBaseManager $orbitalBaseManager
	 * @param PlayerManager $playerManager
	 * @param EntityManager $entityManager
	 * @param array $scores
	 * @param int $minimalScore
	 */
	public function __construct(
		SectorManager $sectorManager,
		SystemManager $systemManager,
		OrbitalBaseManager $orbitalBaseManager,
		PlayerManager $playerManager,
		EntityManager $entityManager,
		$scores,
		$minimalScore
	)
	{
		$this->sectorManager = $sectorManager;
		$this->systemManager = $systemManager;
		$this->orbitalBaseManager = $orbitalBaseManager;
		$this->playerManager = $playerManager;
		$this->entityManager = $entityManager;
		$this->scores = $scores;
		$this->minimalScore = $minimalScore;
	}
	
	/**
	 * @param PlaceOwnerChangeEvent $event
	 */
	public function onPlaceOwnerChange(PlaceOwnerChangeEvent $event)
	{
		$system = $this->systemManager->get($event->getPlace()->rSystem);
		$systems = $this->systemManager->getSectorSystems($system->rSector);
		$sector = $this->sectorManager->get($system->rSector);
		$bases = $this->orbitalBaseManager->getSectorBases($system->rSector);
		$scores = [];
		
		foreach ($bases as $base)
		{
			$player = $this->playerManager->get($base->rPlayer);
			
			$scores[$player->rColor] =
				(!empty($scores[$player->rColor]))
				? $scores[$player->rColor] + $this->scores[$base->typeOfBase]
				: $this->scores[$base->typeOfBase]
			;
		}
		// For each system, the owning faction gains two points
		foreach ($systems as $system) {
			if ($system->rColor === 0) {
				continue;
			}
			$scores[$system->rColor] = (!empty($scores[$system->rColor])) ? $scores[$system->rColor] + 2 : 2;
		}
		$scores[0] = 0;
		arsort($scores);
		reset($scores);
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
