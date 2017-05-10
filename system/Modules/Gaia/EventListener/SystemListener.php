<?php

namespace Asylamba\Modules\Gaia\EventListener;

use Asylamba\Modules\Gaia\Manager\SystemManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Gaia\Event\PlaceOwnerChangeEvent;

class SystemListener
{
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
	 * @param SystemManager $systemManager
	 * @param OrbitalBaseManager $orbitalBaseManager
	 * @param PlayerManager $playerManager
	 * @param EntityManager $entityManager
	 * @param array $scores
	 */
	public function __construct(
		SystemManager $systemManager,
		OrbitalBaseManager $orbitalBaseManager,
		PlayerManager $playerManager,
		EntityManager $entityManager,
		$scores
	)
	{
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
		$bases = $this->orbitalBaseManager->getSystemBases($system);
		
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
		if ($system->rColor !== $newColor && $scores[$newColor] > $scores[$system->rColor]) {
			$system->rColor = $newColor;
			$this->entityManager->flush($system);
		}
	}
}
