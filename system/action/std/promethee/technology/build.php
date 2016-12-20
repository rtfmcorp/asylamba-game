<?php
# building a technology action

# int baseid 		id de la base orbitale
# int techno 	 	id de la technologie

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Promethee\Model\TechnologyQueue;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$database = $this->getContainer()->get('database');
$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$technologyQueueManager = $this->getContainer()->get('promethee.technology_queue_manager');
$technologyHelper = $this->getContainer()->get('promethee.technology_helper');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$researchManager = $this->getContainer()->get('promethee.research_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$techno = $request->query->get('techno');


if ($baseId !== FALSE AND $techno !== FALSE AND in_array($baseId, $verif)) {
	if ($technologyHelper->isATechnology($techno) && !$technologyHelper->isATechnologyNotDisplayed($techno)) {
		
		$S_TQM1 = $technologyQueueManager->getCurrentSession();
		$technologyQueueManager->newSession(ASM_UMODE);
		$technologyQueueManager->load(array('rPlayer' => $session->get('playerId'), 'technology' => $techno));

		if ($technologyQueueManager->size() == 0) {

			$technos = new Technology($session->get('playerId'));
			$targetLevel = $technos->getTechnology($techno) + 1;
			$technologyQueueManager->newSession(ASM_UMODE);
			$technologyQueueManager->load(array('rPlace' => $baseId), array('dEnd'));
			for ($i = 0; $i < $technologyQueueManager->size(); $i++) { 
				if ($technologyQueueManager->get($i)->technology == $techno) {
					$targetLevel++;
				}
			}

			$S_OBM1 = $orbitalBaseManager->getCurrentSession();
			$orbitalBaseManager->newSession(ASM_UMODE);
			$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $session->get('playerId')));

			if ($orbitalBaseManager->size() > 0) {
				$ob = $orbitalBaseManager->get();

				$S_RSM1 = $researchManager->getCurrentSession();
				$researchManager->newSession(ASM_UMODE);
				$researchManager->load(array('rPlayer' => $session->get('playerId')));

				if ($technologyHelper->haveRights($techno, 'resource', $targetLevel, $ob->getResourcesStorage())
					AND $technologyHelper->haveRights($techno, 'credit', $targetLevel, $session->get('playerInfo')->get('credit'))
					AND $technologyHelper->haveRights($techno, 'queue', $ob, $technologyQueueManager->size())
					AND $technologyHelper->haveRights($techno, 'levelPermit', $targetLevel)
					AND $technologyHelper->haveRights($techno, 'technosphereLevel', $ob->getLevelTechnosphere())
					AND ($technologyHelper->haveRights($techno, 'research', $targetLevel, $researchManager->getResearchList($researchManager->get())) === TRUE)
					AND $technologyHelper->haveRights($techno, 'maxLevel', $targetLevel)
					AND $technologyHelper->haveRights($techno, 'baseType', $ob->typeOfBase)) {

					# tutorial
					if ($session->get('playerInfo')->get('stepDone') == FALSE) {
						switch ($session->get('playerInfo')->get('stepTutorial')) {
							case TutorialResource::SHIP0_UNBLOCK:
								if ($techno == Technology::SHIP0_UNBLOCK) {
									TutorialHelper::setStepDone();
								}
								break;
							case TutorialResource::SHIP1_UNBLOCK:
								if ($techno == Technology::SHIP1_UNBLOCK) {
									TutorialHelper::setStepDone();
								}
								break;
						}
					}

					// load du joueur
					$S_PAM1 = $playerManager->getCurrentSession();
					$playerManager->newSession(ASM_UMODE);
					$playerManager->load(array('id' => $session->get('playerId')));

					// construit la nouvelle techno
					$tq = new TechnologyQueue();
					$tq->rPlayer = $session->get('playerId');
					$tq->rPlace = $baseId;
					$tq->technology = $techno;
					$tq->targetLevel = $targetLevel;
					$time = $technologyHelper->getInfo($techno, 'time', $targetLevel);
					$bonusPercent = $session->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED);
					if ($session->get('playerInfo')->get('color') == ColorResource::APHERA) {
						# bonus if the player is from Aphera
						$bonusPercent += ColorResource::BONUS_APHERA_TECHNO;
					}

					# ajout du bonus du lieu
					$bonusPercent += Game::getImprovementFromScientificCoef($ob->planetHistory);
					
					$bonus = round($time * $bonusPercent / 100);
					if ($technologyQueueManager->size() == 0) {
						$tq->dStart = Utils::now();
					} else {
						$tq->dStart = $technologyQueueManager->get($technologyQueueManager->size() - 1)->dEnd;
					}
					$tq->dEnd = Utils::addSecondsToDate($tq->dStart, round($time - $bonus));
					$technologyQueueManager->add($tq);

					// débit resources
					$orbitalBaseManager->decreaseResources($ob, $technologyHelper->getInfo($techno, 'resource', $targetLevel));
					
					// débit des crédits
					$playerManager->decreaseCredit($playerManager->get(), $technologyHelper->getInfo($techno, 'credit', $targetLevel));
					
					// ajout de l'event dans le contrôleur
					$session->get('playerEvent')->add($tq->dEnd, EVENT_BASE, $baseId);

					if (DATA_ANALYSIS) {
						$qr = $database->prepare('INSERT INTO 
							DA_BaseAction(`from`, type, opt1, opt2, weight, dAction)
							VALUES(?, ?, ?, ?, ?, ?)'
						);
						$qr->execute([$session->get('playerId'), 2, $techno, $targetLevel, (DataAnalysis::resourceToStdUnit($technologyHelper->getInfo($techno, 'resource', $targetLevel)) + DataAnalysis::creditToStdUnit($technologyHelper->getInfo($techno, 'credit', $targetLevel))), Utils::now()]);
					}

					// alerte
					$response->flashbag->add('Développement de la technologie programmée', Response::FLASHBAG_SUCCESS);
					$playerManager->changeSession($S_PAM1);
				} else {
					throw new ErrorException('les conditions ne sont pas remplies pour développer une technologie');
				}
				$researchManager->changeSession($S_RSM1);
			} else {
				throw new ErrorException('cette base ne vous appartient pas');	
			}
			$orbitalBaseManager->changeSession($S_OBM1);
		} else {
			throw new ErrorException('Cette technologie est déjà en construction');
		}
		$technologyQueueManager->changeSession($S_TQM1);
	} else {
		throw new ErrorException('la technologie indiquée n\'est pas valide');
	}
} else {
	throw new FormException('pas assez d\'informations pour développer une technologie');
}
