<?php

# building a building action

# int baseid 		id de la base orbitale
# int building 	 	id du bâtiment

use App\Classes\Library\Utils;
use App\Classes\Library\Flashbag;
use App\Classes\Library\DataAnalysis;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Zeus\Resource\TutorialResource;
use App\Modules\Athena\Model\BuildingQueue;
use App\Modules\Zeus\Model\PlayerBonus;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$container = $this->getContainer();
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$buildingQueueManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\BuildingQueueManager::class);
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);
$technologyManager = $this->getContainer()->get(\Asylamba\Modules\Promethee\Manager\TechnologyManager::class);
$tutorialHelper = $this->getContainer()->get(\Asylamba\Modules\Zeus\Helper\TutorialHelper::class);
$request = $this->getContainer()->get('app.request');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$building = $request->query->get('building');

if ($baseId !== FALSE AND $building !== FALSE AND in_array($baseId, $verif)) {
	if ($orbitalBaseHelper->isABuilding($building)) {
		if (($ob = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
			$buildingQueues = $buildingQueueManager->getBaseQueues($baseId);

			$currentLevel = call_user_func(array($ob, 'getReal' . ucfirst($orbitalBaseHelper->getBuildingInfo($building, 'name')) . 'Level'));
			$technos = $technologyManager->getPlayerTechnology($session->get('playerId'));
			if ($orbitalBaseHelper->haveRights($building, $currentLevel + 1, 'resource', $ob->getResourcesStorage())
				AND $orbitalBaseHelper->haveRights(OrbitalBaseResource::GENERATOR, $ob->getLevelGenerator(), 'queue', count($buildingQueues)) 
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
				$nbBuildingQueues = count($buildingQueues);
				if ($nbBuildingQueues === 0) {
					$bq->dStart = Utils::now();
				} else {
					$bq->dStart = $buildingQueues[$nbBuildingQueues - 1]->dEnd;
				}
				$bq->dEnd = Utils::addSecondsToDate($bq->dStart, round($time - $bonus));
				$buildingQueueManager->add($bq);

				# debit resources
				$orbitalBaseManager->decreaseResources($ob, $orbitalBaseHelper->getBuildingInfo($building, 'level', $currentLevel + 1, 'resourcePrice'));

				if ($container->getParameter('data_analysis')) {
					$qr = $database->prepare('INSERT INTO 
						DA_BaseAction(`from`, type, opt1, opt2, weight, dAction)
						VALUES(?, ?, ?, ?, ?, ?)'
					);
					$qr->execute([$session->get('playerId'), 1, $building, $currentLevel + 1, DataAnalysis::resourceToStdUnit($orbitalBaseHelper->getBuildingInfo($building, 'level', $currentLevel + 1, 'resourcePrice')), Utils::now()]);
				}

				# add the event in controller
				$session->get('playerEvent')->add($bq->dEnd, $container->getParameter('event_base'), $baseId);

				$session->addFlashbag('Construction programmée', Flashbag::TYPE_SUCCESS);
			} else {
				throw new FormException('les conditions ne sont pas remplies pour construire ce bâtiment');
			}
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
	} else {
		throw new ErrorException('le bâtiment indiqué n\'est pas valide');
	}
} else {
	throw new FormException('pas assez d\'informations pour construire un bâtiment');
}
