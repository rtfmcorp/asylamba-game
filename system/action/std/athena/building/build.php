<?php

# building a building action

# int baseid 		id de la base orbitale
# int building 	 	id du bâtiment

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Athena\Model\BuildingQueue;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$database = $this->getContainer()->get('database');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$buildingQueueManager = $this->getContainer()->get('athena.building_queue_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$technologyManager = $this->getContainer()->get('promethee.technology_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');
$request = $this->getContainer()->get('app.request');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$building = $request->query->get('building');

if ($baseId !== FALSE AND $building !== FALSE AND in_array($baseId, $verif)) {
	if ($orbitalBaseHelper->isABuilding($building)) {
		if (($ob = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
			$S_BQM1 = $buildingQueueManager->getCurrentSession();
			$buildingQueueManager->newSession(ASM_UMODE);
			$buildingQueueManager->load(array('rOrbitalBase' => $baseId), array('dEnd'));

			$currentLevel = call_user_func(array($ob, 'getReal' . ucfirst($orbitalBaseHelper->getBuildingInfo($building, 'name')) . 'Level'));
			$technos = $technologyManager->getPlayerTechnology($session->get('playerId'));
			if ($orbitalBaseHelper->haveRights($building, $currentLevel + 1, 'resource', $ob->getResourcesStorage())
				AND $orbitalBaseHelper->haveRights(OrbitalBaseResource::GENERATOR, $ob->getLevelGenerator(), 'queue', $buildingQueueManager->size()) 
				AND ($orbitalBaseHelper->haveRights($building, $currentLevel + 1, 'buildingTree', $ob) === TRUE)
				AND $orbitalBaseHelper->haveRights($building, $currentLevel + 1, 'techno', $technos)) {

				# tutorial
				if ($session->get('playerInfo')->get('stepDone') == FALSE) {
					switch ($session->get('playerInfo')->get('stepTutorial')) {
						case TutorialResource::GENERATOR_LEVEL_2:
							if ($building == OrbitalBaseResource::GENERATOR AND $currentLevel + 1 >= 2) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::REFINERY_LEVEL_3:
							if ($building == OrbitalBaseResource::REFINERY AND $currentLevel + 1 >= 3) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::STORAGE_LEVEL_3:
							if ($building == OrbitalBaseResource::STORAGE AND $currentLevel + 1 >= 3) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::DOCK1_LEVEL_1:
							if ($building == OrbitalBaseResource::DOCK1 AND $currentLevel + 1 >= 1) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::TECHNOSPHERE_LEVEL_1:
							if ($building == OrbitalBaseResource::TECHNOSPHERE AND $currentLevel + 1 >= 1) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::REFINERY_LEVEL_10:
							if ($building == OrbitalBaseResource::REFINERY AND $currentLevel + 1 >= 10) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::STORAGE_LEVEL_8:
							if ($building == OrbitalBaseResource::STORAGE AND $currentLevel + 1 >= 8) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::DOCK1_LEVEL_6:
							if ($building == OrbitalBaseResource::DOCK1 AND $currentLevel + 1 >= 6) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::REFINERY_LEVEL_16:
							if ($building == OrbitalBaseResource::REFINERY AND $currentLevel + 1 >= 16) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::STORAGE_LEVEL_12:
							if ($building == OrbitalBaseResource::STORAGE AND $currentLevel + 1 >= 12) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::TECHNOSPHERE_LEVEL_6:
							if ($building == OrbitalBaseResource::TECHNOSPHERE AND $currentLevel + 1 >= 6) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::DOCK1_LEVEL_15:
							if ($building == OrbitalBaseResource::DOCK1 AND $currentLevel + 1 >= 15) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::REFINERY_LEVEL_20:
							if ($building == OrbitalBaseResource::REFINERY AND $currentLevel + 1 >= 20) {
								$tutorialHelper->setStepDone();
							}
							break;
					}
				}

				# build the new building
				$bq = new BuildingQueue();
				$bq->rOrbitalBase = $baseId;
				$bq->buildingNumber = $building;
				$bq->targetLevel = $currentLevel + 1;
				$time = $orbitalBaseHelper->getBuildingInfo($building, 'level', $currentLevel + 1, 'time');
				$bonus = $time * $session->get('playerBonus')->get(PlayerBonus::GENERATOR_SPEED) / 100;
				if ($buildingQueueManager->size() == 0) {
					$bq->dStart = Utils::now();
				} else {
					$bq->dStart = $buildingQueueManager->get($buildingQueueManager->size() - 1)->dEnd;
				}
				$bq->dEnd = Utils::addSecondsToDate($bq->dStart, round($time - $bonus));
				$buildingQueueManager->add($bq);

				# debit resources
				$orbitalBaseManager->decreaseResources($ob, $orbitalBaseHelper->getBuildingInfo($building, 'level', $currentLevel + 1, 'resourcePrice'));

				if (DATA_ANALYSIS) {
					$qr = $database->prepare('INSERT INTO 
						DA_BaseAction(`from`, type, opt1, opt2, weight, dAction)
						VALUES(?, ?, ?, ?, ?, ?)'
					);
					$qr->execute([$session->get('playerId'), 1, $building, $currentLevel + 1, DataAnalysis::resourceToStdUnit($orbitalBaseHelper->getBuildingInfo($building, 'level', $currentLevel + 1, 'resourcePrice')), Utils::now()]);
				}

				# add the event in controller
				$session->get('playerEvent')->add($bq->dEnd, EVENT_BASE, $baseId);

				$session->addFlashbag('Construction programmée', Flashbag::TYPE_SUCCESS);
			} else {
				throw new ErrorException('les conditions ne sont pas remplies pour construire ce bâtiment');
			}
			$buildingQueueManager->changeSession($S_BQM1);
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
	} else {
		throw new ErrorException('le bâtiment indiqué n\'est pas valide');
	}
} else {
	throw new FormException('pas assez d\'informations pour construire un bâtiment');
}