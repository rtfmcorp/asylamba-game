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
	
	/**
	 * @param SectorManager $sectorManager
	 * @param SystemManager $systemManager
	 * @param OrbitalBaseManager $orbitalBaseManager
	 * @param PlayerManager $playerManager
	 * @param EntityManager $entityManager
	 * @param array $scores
	 */
	public function __construct(
		SectorManager $sectorManager,
		SystemManager $systemManager,
		OrbitalBaseManager $orbitalBaseManager,
		PlayerManager $playerManager,
		EntityManager $entityManager,
		$scores
	)
	{
		$this->sectorManager = $sectorManager;
		$this->systemManager = $systemManager;
		$this->orbitalBaseManager = $orbitalBaseManager;
		$this->playerManager = $playerManager;
		$this->entityManager = $entityManager;
		$this->scores = $scores;
	}
	
	/**
	 * @param PlaceOwnerChangeEvent $event
	 */
	public function onPlaceOwnerChange(PlaceOwnerChangeEvent $event)
	{
		$system = $this->systemManager->get($event->getPlace()->rSystem);
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
		arsort($scores);
		reset($scores);
		$newColor = key($scores);
		if ($sector->rColor !== $newColor) {
			$sector->rColor = $newColor;
			$this->entityManager->flush($sector);
		}
	}
}
