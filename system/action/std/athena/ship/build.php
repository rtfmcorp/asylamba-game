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

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$technologyManager = $this->getContainer()->get('promethee.technology_manager');
$shipQueueManager = $this->getContainer()->get('athena.ship_queue_manager');
$shipHelper = $this->getContainer()->get('athena.ship_helper');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$database = $this->getContainer()->get('database');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$ship = $request->query->get('ship');
$quantity = $request->query->get('quantity');

if ($baseId !== FALSE AND $ship !== FALSE AND $quantity !== FALSE AND in_array($baseId, $verif) AND $quantity != 0) { 
	if (ShipResource::isAShip($ship)) {
		$S_OBM1 = $orbitalBaseManager->getCurrentSession();
		$orbitalBaseManager->newSession(ASM_UMODE);
		$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $session->get('playerId')));

		if ($orbitalBaseManager->size() > 0) {
			$ob  = $orbitalBaseManager->get();
			if ($orbitalBaseHelper->isAShipFromDock1($ship)) {
				$dockType = 1;
			} elseif ($orbitalBaseHelper->isAShipFromDock2($ship)) {
				$dockType = 2;
				$quantity = 1;
			} else {
				$dockType = 3;
				$quantity = 1;
			}
			$S_SQM1 = $shipQueueManager->getCurrentSession();
			$shipQueueManager->newSession(ASM_UMODE);
			$shipQueueManager->load(array('rOrbitalBase' => $baseId, 'dockType' => $dockType), array('dEnd'));
			$technos = $technologyManager->getPlayerTechnology($session->get('playerId'));
			if ($shipHelper->haveRights($ship, 'resource', $ob->getResourcesStorage(), $quantity)
				AND $shipHelper->haveRights($ship, 'queue', $ob, $shipQueueManager->size())
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
				if ($shipQueueManager->size() == 0) {
					$sq->dStart = Utils::now();
				} else {
					$sq->dStart = $shipQueueManager->get($shipQueueManager->size() - 1)->dEnd;
				}
				$sq->dEnd = Utils::addSecondsToDate($sq->dStart, round($time - $bonus));
				$shipQueueManager->add($sq);
				
				// débit des ressources au joueur
				$resourcePrice = ShipResource::getInfo($ship, 'resourcePrice') * $quantity;
				if ($ship == ShipResource::CERBERE || $ship == ShipResource::PHENIX) {

					$_CLM1 = $colorManager->getCurrentSession();
					$colorManager->newSession();
					$colorManager->load(['id' => $session->get('playerInfo')->get('color')]);
					if (in_array(ColorResource::PRICEBIGSHIPBONUS, $colorManager->get()->bonus)) {
						$resourcePrice -= round($resourcePrice * ColorResource::BONUS_EMPIRE_CRUISER / 100);
					}
					$colorManager->changeSession($_CLM1);
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
			$shipQueueManager->changeSession($S_SQM1);
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
		$orbitalBaseManager->changeSession($S_OBM1);
	} else {
		throw new ErrorException('construction de vaisseau impossible - vaisseau inconnu');
	}
} else {
	throw new FormException('pas assez d\'informations pour construire un vaisseau');
}