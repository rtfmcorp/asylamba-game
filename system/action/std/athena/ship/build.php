<?php
# build ship action

# int baseid 		id (rPlace) de la base orbitale
# int ship 			id du vaisseau
# int quantity 		nombre de vaisseaux à construire


use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Athena\Model\ShipQueue;

use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);
$technologyManager = $this->getContainer()->get(\Asylamba\Modules\Promethee\Manager\TechnologyManager::class);
$shipQueueManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\ShipQueueManager::class);
$shipHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\ShipHelper::class);
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);
$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$tutorialHelper = $this->getContainer()->get(\Asylamba\Modules\Zeus\Helper\TutorialHelper::class);

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$ship = $request->query->get('ship');
$quantity = $request->query->get('quantity');

if ($baseId !== FALSE AND $ship !== FALSE AND $quantity !== FALSE AND in_array($baseId, $verif) AND $quantity != 0) { 
	if (ShipResource::isAShip($ship)) {
		if (($ob  = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
			if ($orbitalBaseHelper->isAShipFromDock1($ship)) {
				$dockType = 1;
			} elseif ($orbitalBaseHelper->isAShipFromDock2($ship)) {
				$dockType = 2;
				$quantity = 1;
			} else {
				$dockType = 3;
				$quantity = 1;
			}
			$shipQueues = $shipQueueManager->getByBaseAndDockType($baseId, $dockType);
			$nbShipQueues = count($shipQueues);
			$technos = $technologyManager->getPlayerTechnology($session->get('playerId'));
			if ($shipHelper->haveRights($ship, 'resource', $ob->getResourcesStorage(), $quantity)
				AND $shipHelper->haveRights($ship, 'queue', $ob, $nbShipQueues)
				AND $shipHelper->haveRights($ship, 'shipTree', $ob)
				AND $shipHelper->haveRights($ship, 'pev', $ob, $quantity)
				AND $shipHelper->haveRights($ship, 'techno', $technos)) {

				# tutorial
				if ($session->get('playerInfo')->get('stepDone') == FALSE) {
					switch ($session->get('playerInfo')->get('stepTutorial')) {
						case TutorialResource::BUILD_SHIP0:
							if ($ship == ShipResource::PEGASE) {
								$tutorialHelper->setStepDone();
							}
							break;
						case TutorialResource::BUILD_SHIP1:
							if ($ship == ShipResource::SATYRE) {
								$tutorialHelper->setStepDone();
							}
							break;
					}
				}

				// construit le(s) nouveau(x) vaisseau(x)
				$sq = new ShipQueue();
				$sq->rOrbitalBase = $baseId;
				$sq->dockType = $dockType;
				$sq->shipNumber = $ship;
				$sq->quantity = $quantity;

				$time = ShipResource::getInfo($ship, 'time') * $quantity;
				switch ($dockType) {
					case 1:
						$playerBonus = PlayerBonus::DOCK1_SPEED;
						break;
					case 2:
						$playerBonus = PlayerBonus::DOCK2_SPEED;
						break;
					case 3:
						$playerBonus = PlayerBonus::DOCK3_SPEED;
						break;
				}
				$bonus = $time * $session->get('playerBonus')->get($playerBonus) / 100;
				
				$sq->dStart = ($nbShipQueues === 0) ? Utils::now() : $shipQueues[$nbShipQueues - 1]->dEnd;
				$sq->dEnd = Utils::addSecondsToDate($sq->dStart, round($time - $bonus));
				
				$shipQueueManager->add($sq);
				
				// débit des ressources au joueur
				$resourcePrice = ShipResource::getInfo($ship, 'resourcePrice') * $quantity;
				if ($ship == ShipResource::CERBERE || $ship == ShipResource::PHENIX) {
					if (in_array(ColorResource::PRICEBIGSHIPBONUS, $colorManager->get($session->get('playerInfo')->get('color'))->bonus)) {
						$resourcePrice -= round($resourcePrice * ColorResource::BONUS_EMPIRE_CRUISER / 100);
					}
				}
				$orbitalBaseManager->decreaseResources($ob, $resourcePrice);

				// ajout de l'event dans le contrôleur
				$session->get('playerEvent')->add($sq->dEnd, EVENT_BASE, $baseId);

				if (DATA_ANALYSIS) {
					$qr = $database->prepare('INSERT INTO 
						DA_BaseAction(`from`, type, opt1, opt2, weight, dAction)
						VALUES(?, ?, ?, ?, ?, ?)'
					);
					$qr->execute([$session->get('playerId'), 3, $ship, $quantity, DataAnalysis::resourceToStdUnit(ShipResource::getInfo($ship, 'resourcePrice') * $quantity), Utils::now()]);
				}

				// alerte
				if ($quantity == 1) {
					$session->addFlashbag('Construction d\'' . (ShipResource::isAFemaleShipName($ship) ? 'une ' : 'un ') . ShipResource::getInfo($ship, 'codeName') . ' commandée', Flashbag::TYPE_SUCCESS);
				} else {
					$session->addFlashbag('Construction de ' . $quantity . ' ' . ShipResource::getInfo($ship, 'codeName') . Format::addPlural($quantity) . ' commandée', Flashbag::TYPE_SUCCESS);
				}
			} else {
				throw new ErrorException('les conditions ne sont pas remplies pour construire ce vaisseau');
			}
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
	} else {
		throw new ErrorException('construction de vaisseau impossible - vaisseau inconnu');
	}
} else {
	throw new FormException('pas assez d\'informations pour construire un vaisseau');
}
