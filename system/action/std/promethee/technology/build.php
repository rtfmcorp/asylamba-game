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
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$technologyQueueManager = $this->getContainer()->get(\Asylamba\Modules\Promethee\Manager\TechnologyQueueManager::class);
$technologyHelper = $this->getContainer()->get(\Asylamba\Modules\Promethee\Helper\TechnologyHelper::class);
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$researchManager = $this->getContainer()->get(\Asylamba\Modules\Promethee\Manager\ResearchManager::class);
$technologyManager = $this->getContainer()->get(\Asylamba\Modules\Promethee\Manager\TechnologyManager::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$tutorialHelper = $this->getContainer()->get(\Asylamba\Modules\Zeus\Helper\TutorialHelper::class);

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$techno = $request->query->get('techno');


if ($baseId !== FALSE AND $techno !== FALSE AND in_array($baseId, $verif)) {
	if ($technologyHelper->isATechnology($techno) && !$technologyHelper->isATechnologyNotDisplayed($techno)) {
		if (($technologyQueueManager->getPlayerTechnologyQueue($session->get('playerId'), $techno)) === null) {

			$technos = $technologyManager->getPlayerTechnology($session->get('playerId'));
			$targetLevel = $technos->getTechnology($techno) + 1;
			// @TODO I think this piece of code is dead
			$technologyQueues = $technologyQueueManager->getPlaceQueues($baseId);
			$nbTechnologyQueues = count($technologyQueues);
			foreach ($technologyQueues as $technologyQueue) { 
				if ($technologyQueue->technology == $techno) {
					$targetLevel++;
				}
			}
			if (($ob = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
				$S_RSM1 = $researchManager->getCurrentSession();
				$researchManager->newSession(ASM_UMODE);
				$researchManager->load(array('rPlayer' => $session->get('playerId')));

				if ($technologyHelper->haveRights($techno, 'resource', $targetLevel, $ob->getResourcesStorage())
					AND $technologyHelper->haveRights($techno, 'credit', $targetLevel, $session->get('playerInfo')->get('credit'))
					AND $technologyHelper->haveRights($techno, 'queue', $ob, $nbTechnologyQueues)
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
									$tutorialHelper->setStepDone();
								}
								break;
							case TutorialResource::SHIP1_UNBLOCK:
								if ($techno == Technology::SHIP1_UNBLOCK) {
									$tutorialHelper->setStepDone();
								}
								break;
						}
					}

					// construit la nouvelle techno
					$time = $technologyHelper->getInfo($techno, 'time', $targetLevel);
					$bonusPercent = $session->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED);
					if ($session->get('playerInfo')->get('color') == ColorResource::APHERA) {
						# bonus if the player is from Aphera
						$bonusPercent += ColorResource::BONUS_APHERA_TECHNO;
					}

					# ajout du bonus du lieu
					$bonusPercent += Game::getImprovementFromScientificCoef($ob->planetHistory);
					$bonus = round($time * $bonusPercent / 100);
					
					$createdAt = 
						($nbTechnologyQueues === 0)
						? Utils::now()
						: $technologyQueues[$nbTechnologyQueues - 1]->getEndedAt()
					;
					$tq = 
						(new TechnologyQueue())
						->setPlayerId($session->get('playerId'))
						->setPlaceId($baseId)
						->setTechnology($techno)
						->setTargetLevel($targetLevel)
						->setCreatedAt($createdAt)
						->setEndedAt(Utils::addSecondsToDate($createdAt, round($time - $bonus)))
					;
					$technologyQueueManager->add($tq);

					// débit resources
					$orbitalBaseManager->decreaseResources($ob, $technologyHelper->getInfo($techno, 'resource', $targetLevel));
					
					// débit des crédits
					$playerManager->decreaseCredit($playerManager->get($session->get('playerId')), $technologyHelper->getInfo($techno, 'credit', $targetLevel));

					if (DATA_ANALYSIS) {
						$qr = $database->prepare('INSERT INTO 
							DA_BaseAction(`from`, type, opt1, opt2, weight, dAction)
							VALUES(?, ?, ?, ?, ?, ?)'
						);
						$qr->execute([$session->get('playerId'), 2, $techno, $targetLevel, (DataAnalysis::resourceToStdUnit($technologyHelper->getInfo($techno, 'resource', $targetLevel)) + DataAnalysis::creditToStdUnit($technologyHelper->getInfo($techno, 'credit', $targetLevel))), Utils::now()]);
					}

					// alerte
					$session->addFlashbag('Développement de la technologie programmée', Flashbag::TYPE_SUCCESS);
				} else {
					throw new ErrorException('les conditions ne sont pas remplies pour développer une technologie');
				}
				$researchManager->changeSession($S_RSM1);
			} else {
				throw new ErrorException('cette base ne vous appartient pas');	
			}
		} else {
			throw new ErrorException('Cette technologie est déjà en construction');
		}
	} else {
		throw new ErrorException('la technologie indiquée n\'est pas valide');
	}
} else {
	throw new FormException('pas assez d\'informations pour développer une technologie');
}
