<?php

/**
 * TutorialHelper
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @update 25.04.14
 */
namespace Asylamba\Modules\Zeus\Helper;

use Asylamba\Classes\Container\Session;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Athena\Manager\BuildingQueueManager;
use Asylamba\Modules\Promethee\Manager\TechnologyQueueManager;
use Asylamba\Modules\Promethee\Manager\TechnologyManager;

class TutorialHelper {
	/** @var EntityManager **/
	protected $entityManager;
	/** @var PlayerManager **/
	protected $playerManager;
	/** @var OrbitalBaseManager **/
	protected $orbitalBaseManager;
	/** @var BuildingQueueManager **/
	protected $buildingQueueManager;
	/** @var TechnologyQueueManager **/
	protected $technologyQueueManager;
	/** @var TechnologyManager **/
	protected $technologyManager;
	/** @var Session **/
	protected $session;
	
	/**
	 * @param EntityManager $entityManager
	 * @param PlayerManager $playerManager
	 * @param OrbitalBaseManager $orbitalBaseManager
	 * @param BuildingQueueManager $buildingQueueManager
	 * @param TechnologyQueueManager $technologyQueueManager
	 * @param TechnologyManager $technologyManager
	 * @param Session $session
	 */
	public function __construct(
		EntityManager $entityManager,
		PlayerManager $playerManager,
		OrbitalBaseManager $orbitalBaseManager,
		BuildingQueueManager $buildingQueueManager,
		TechnologyQueueManager $technologyQueueManager,
		TechnologyManager $technologyManager,
		Session $session
	)
	{
		$this->entityManager = $entityManager;
		$this->playerManager = $playerManager;
		$this->orbitalBaseManager = $orbitalBaseManager;
		$this->buildingQueueManager = $buildingQueueManager;
		$this->technologyQueueManager = $technologyQueueManager;
		$this->technologyManager = $technologyManager;
		$this->session = $session;
	}
	
	public function checkTutorial() {
		# PAS UTILISEE POUR L'INSTANT (le sera quand il y aura une étape passive dans le tutoriel)
		$player = $this->session->get('playerId');
		$stepTutorial = $this->session->get('playerInfo')->get('stepTutorial');
		$stepDone = $this->session->get('playerInfo')->get('stepDone');

		if ($stepTutorial > 0) {
			if ($stepDone == FALSE) {
				# check if current step is done

				# hint : checker seulement les actions passives
				switch ($stepTutorial) {
					case 1:
						$asdf = 'asdf';
						break;
					case 2:
						$jlk = 'jkl';
						break;
				}
			} 
		}
	}

	public function setStepDone() {
		$player = $this->playerManager->get($this->session->get('playerId'));
		
		$player->stepDone = TRUE;

		$this->session->get('playerInfo')->add('stepDone', TRUE);
		
		$this->entityManager->flush($player);
	}

	public function clearStepDone() {
		$player = $this->playerManager->get($this->session->get('playerId'));
		
		$player->stepDone = FALSE;

		$this->session->get('playerInfo')->add('stepDone', FALSE);

		$this->entityManager->flush($player);
	}

	public function isNextBuildingStepAlreadyDone($playerId, $buildingId, $level) {
		$nextStepAlreadyDone = FALSE;

		$S_OBM2 = $this->orbitalBaseManager->getCurrentSession();
		$this->orbitalBaseManager->newSession();
		$this->orbitalBaseManager->load(array('rPlayer' => $playerId));
		for ($i = 0; $i < $this->orbitalBaseManager->size() ; $i++) { 
			$orbitalBase = $this->orbitalBaseManager->get($i);
			if ($orbitalBase->getBuildingLevel($buildingId) >= $level) {
				$nextStepAlreadyDone = TRUE;
				break;
			} else {
				# verify in the queue
				$S_BQM2 = $this->buildingQueueManager->getCurrentSession();
				$this->buildingQueueManager->newSession();
				$this->buildingQueueManager->load(array('rOrbitalBase' => $orbitalBase->rPlace));
				for ($i = 0; $i < $this->buildingQueueManager->size() ; $i++) { 
					$buildingQueue = $this->buildingQueueManager->get($i);
					if ($buildingQueue->buildingNumber == $buildingId AND $buildingQueue->targetLevel >= $level) {
						$nextStepAlreadyDone = TRUE;
						break;
					} 
				}
				$this->buildingQueueManager->changeSession($S_BQM2);
			}
		}
		$this->orbitalBaseManager->changeSession($S_OBM2);

		return $nextStepAlreadyDone;
	}

	public function isNextTechnoStepAlreadyDone($playerId, $technoId, $level = 1) {
		$nextStepAlreadyDone = FALSE;

		$technology = $this->technologyManager->getPlayerTechnology($playerId);
		if ($technology->getTechnology($technoId) >= $level) {
			$nextStepAlreadyDone = TRUE;
		} else {
			# verify in the queue
			$S_TQM2 = $this->technologyQueueManager->getCurrentSession();
			$this->technologyQueueManager->newSession();
			$this->technologyQueueManager->load(array('rPlayer' => $playerId));
			for ($i = 0; $i < $this->technologyQueueManager->size() ; $i++) { 
				$technologyQueue = $this->technologyQueueManager->get($i);
				if ($technologyQueue->technology == $technoId) {
					$nextStepAlreadyDone = TRUE;
					break;
				} 
			}
			$this->technologyQueueManager->changeSession($S_TQM2);
		}

		return $nextStepAlreadyDone;
	}
}