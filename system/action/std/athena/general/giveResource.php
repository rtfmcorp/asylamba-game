<?php
# give resources action

# int baseid 		id (rPlace) de la base orbitale
# int otherbaseid 	id (rPlace) de la base orbitale à qui on veut envoyer des ressources
# int quantity 		quantité de ressources à envoyer

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Hermes\Model\Notification;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$commercialShippingManager = $this->getContainer()->get('athena.commercial_shipping_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');

for ($i = 0; $i < $placeManager->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $placeManager->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$otherBaseId = $request->query->get('otherbaseid');
$quantity = $request->request->get('quantity');

if ($baseId !== FALSE AND $otherBaseId !== FALSE AND $quantity !== FALSE AND in_array($baseId, $verif)) {
	if ($baseId != $otherBaseId) {

		$resource = intval($quantity);

		$S_OBM1 = $orbitalBaseManager->getCurrentSession();
		$orbitalBaseManager->newSession(ASM_UMODE);
		$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $placeManager->get('playerId')));

		if ($orbitalBaseManager->size() > 0) {
			$orbitalBase = $orbitalBaseManager->get();

			if ($resource > 0) {
				if ($orbitalBase->getResourcesStorage() >= $resource) {
					//---------------------------
					# controler le nombre de vaisseaux
					# verif : have we enough commercialShips
					$totalShips = $orbitalBaseHelper->getBuildingInfo(6, 'level', $orbitalBase->getLevelCommercialPlateforme(), 'nbCommercialShip');
					$usedShips = 0;

					$S_CSM1 = $commercialShippingManager->getCurrentSession();
					$commercialShippingManager->changeSession($orbitalBase->shippingManager);
					for ($i = 0; $i < $commercialShippingManager->size(); $i++) { 
						if ($commercialShippingManager->get($i)->rBase == $orbitalBase->rPlace) {
							$usedShips += $commercialShippingManager->get($i)->shipQuantity;
						}
					}
					$commercialShippingManager->changeSession($S_CSM1);

					$remainingShips = $totalShips - $usedShips;
					$commercialShipQuantity = Game::getCommercialShipQuantityNeeded(Transaction::TYP_RESOURCE, $resource);

					if ($remainingShips >= $commercialShipQuantity) {
						
						$orbitalBaseManager->load(array('rPlace' => $otherBaseId));
						if ($orbitalBaseManager->size() == 2) {
							$otherBase = $orbitalBaseManager->get(1);

							# load places to compute travel time
							$S_PLM1 = $placeManager->getCurrentSession();
							$placeManager->newSession(ASM_UMODE);
							$placeManager->load(array('id' => $orbitalBase->rPlace));
							$placeManager->load(array('id' => $otherBase->rPlace));
							$timeToTravel = Game::getTimeToTravelCommercial($placeManager->get(0), $placeManager->get(1));
							$departure = Utils::now();
							$arrival = Utils::addSecondsToDate($departure, $timeToTravel);
							$placeManager->changeSession($S_PLM1);

							# création du convoi
							$cs = new CommercialShipping();
							$cs->rPlayer = $placeManager->get('playerId');
							$cs->rBase = $orbitalBase->rPlace;
							$cs->rBaseDestination = $otherBase->rPlace;
							$cs->resourceTransported = $resource;
							$cs->shipQuantity = $commercialShipQuantity;
							$cs->dDeparture = $departure;
							$cs->dArrival = $arrival;
							$cs->statement = CommercialShipping::ST_GOING;
							$commercialShippingManager->add($cs);

							$orbitalBaseManager->decreaseResources($orbitalBase, $resource);

							if ($orbitalBase->getRPlayer() != $otherBase->getRPlayer()) {
								$n = new Notification();
								$n->setRPlayer($otherBase->getRPlayer());
								$n->setTitle('Envoi de ressources');
								$n->addBeg()->addTxt($otherBase->getName())->addSep();
								$n->addLnk('embassy/player-' . $placeManager->get('playerId'), $placeManager->get('playerInfo')->get('name'));
								$n->addTxt(' a lancé un convoi de ')->addStg(Format::numberFormat($resource))->addTxt(' ressources depuis sa base ');
								$n->addLnk('map/place-' . $orbitalBase->getRPlace(), $orbitalBase->getName())->addTxt('. ');
								$n->addBrk()->addTxt('Quand le convoi arrivera, les ressources seront à vous.');
								$n->addSep()->addLnk('bases/base-' . $otherBase->getId()  . '/view-commercialplateforme/mode-market', 'vers la place du commerce →');
								$n->addEnd();
								$notificationManager->add($n);
							}

							if (DATA_ANALYSIS) {
								$qr = $database->prepare('INSERT INTO 
									DA_CommercialRelation(`from`, `to`, type, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
								);
								$qr->execute([$placeManager->get('playerId'), $otherBase->getRPlayer(), 4, DataAnalysis::resourceToStdUnit($resource), Utils::now()]);
							}

							$session->addFlashbag('Ressources envoyées', Flashbag::TYPE_SUCCESS);
						} else {
							throw new ErrorException('envoi de ressources impossible - erreur dans les bases orbitales');
						}
					} else {
						throw new ErrorException('envoi de ressources impossible - vous n\'avez pas assez de vaisseaux de transport');
					}
				} else {
					throw new ErrorException('envoi de ressources impossible - vous ne pouvez pas envoyer plus que ce que vous possédez');
				}
			} else {
				throw new ErrorException('envoi de ressources impossible - il faut envoyer un nombre entier positif');
			}
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
		$orbitalBaseManager->changeSession($S_OBM1);
	} else {
		throw new ErrorException('envoi de ressources impossible - action inutile, vous ressources sont déjà sur cette base orbitale');
	}
} else {
	throw new FormException('pas assez d\'informations pour envoyer des ressources');
}