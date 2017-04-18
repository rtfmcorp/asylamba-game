<?php
# give resources action

# int baseid 		id (rPlace) de la base orbitale
# int otherbaseid 	id (rPlace) de la base orbitale à qui on veut envoyer des ressources
# int quantity 		quantité de ressources à envoyer
# [int identifier]	shipId

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$commercialShippingManager = $this->getContainer()->get('athena.commercial_shipping_manager');
$transactionManager = $this->getContainer()->get('athena.transaction_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$otherBaseId = $request->request->get('otherbaseid');

if ($baseId !== FALSE AND $otherBaseId !== FALSE AND in_array($baseId, $verif)) {
	if ($baseId != $otherBaseId) {
		for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) { 
			if ($request->request->has('identifier-' . $i)) {
				$shipType = $i;
				$shipName = ShipResource::getInfo($i, 'codeName');

				if ($request->request->has('quantity-' . $i)) {
					$ships = $request->request->get('quantity-' . $i) > 0
						? $request->request->get('quantity-' . $i) : 1;
					$ships = intval($ships);
				}

				break;
			}
		}

		if (isset($shipType) AND isset($ships)) {
			if (($orbitalBase = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
				if (ShipResource::isAShipFromDock1($shipType) OR ShipResource::isAShipFromDock2($shipType)) {
					if ($ships > 0) {
						if ($orbitalBase->getShipStorage($shipType) >= $ships) {
							$commercialShipQuantity = Game::getCommercialShipQuantityNeeded(Transaction::TYP_SHIP, $ships, $shipType);
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

							if ($remainingShips >= $commercialShipQuantity) {
								if (($otherBase = $orbitalBaseManager->get($otherBaseId)) !== null) {
									# load places to compute travel time
									$startPlace = $placeManager->get($orbitalBase->rPlace);
									$destinationPlace = $placeManager->get($otherBase->rPlace);
									$timeToTravel = Game::getTimeToTravelCommercial($startPlace, $destinationPlace);
									$departure = Utils::now();
									$arrival = Utils::addSecondsToDate($departure, $timeToTravel);

									# création de la transaction
									$tr = new Transaction();
									$tr->rPlayer = $session->get('playerId');
									$tr->rPlace = $orbitalBase->rPlace;
									$tr->type = Transaction::TYP_SHIP; 
									$tr->quantity = $ships;
									$tr->identifier = $shipType;
									$tr->price = 0;
									$tr->commercialShipQuantity = $commercialShipQuantity;
									$tr->statement = Transaction::ST_COMPLETED;
									$tr->dPublication = Utils::now();
									$transactionManager->add($tr);

									# création du convoi
									$cs = new CommercialShipping();
									$cs->rPlayer = $session->get('playerId');
									$cs->rBase = $orbitalBase->rPlace;
									$cs->rBaseDestination = $otherBase->rPlace;
									$cs->rTransaction = $tr->id;
									$cs->resourceTransported = 0;
									$cs->shipQuantity = $commercialShipQuantity;
									$cs->dDeparture = $departure;
									$cs->dArrival = $arrival;
									$cs->statement = CommercialShipping::ST_GOING;
									$commercialShippingManager->add($cs);

									$orbitalBase->setShipStorage($shipType, $orbitalBase->getShipStorage($shipType) - $ships);

									if ($orbitalBase->getRPlayer() != $otherBase->getRPlayer()) {
										$n = new Notification();
										$n->setRPlayer($otherBase->getRPlayer());
										$n->setTitle('Envoi de vaisseaux');
										$n->addBeg()->addTxt($otherBase->getName())->addSep();
										$n->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'));
										$n->addTxt(' a lancé un convoi de ')->addStg(Format::numberFormat($ships))->addTxt(' ' . $shipName . ' depuis sa base ');
										$n->addLnk('map/place-' . $orbitalBase->getRPlace(), $orbitalBase->getName())->addTxt('. ');
										$n->addBrk()->addTxt('Quand le convoi arrivera, les vaisseaux seront placés dans votre hangar.');
										$n->addSep()->addLnk('bases/base-' . $otherBase->getId()  . '/view-commercialplateforme/mode-market', 'vers la place du commerce →');
										$n->addEnd();
										
										$notificationManager->add($n);
									}

									if (DATA_ANALYSIS) {
										$qr = $this->getContainer()->get('database')->prepare('INSERT INTO 
											DA_CommercialRelation(`from`, `to`, type, weight, dAction)
											VALUES(?, ?, ?, ?, ?)'
										);
										$qr->execute([$session->get('playerId'), $otherBase->getRPlayer(), 3, DataAnalysis::resourceToStdUnit(ShipResource::getInfo($shipType, 'resourcePrice') * $ships), Utils::now()]);
									}
									$session->addFlashbag('Vaisseaux envoyés', Flashbag::TYPE_SUCCESS);
								} else {
									throw new ErrorException('Erreur dans les bases orbitales');
								}
							} else {
								throw new ErrorException('Vous n\'avez pas assez de vaisseaux de transport');
							}
						} else {
							throw new ErrorException('Vous n\'avez pas assez de vaisseaux de ce type');
						}
					} else {
						throw new ErrorException('Envoi de vaisseaux impossible');
					}
				} else {
					throw new ErrorException('Vaisseau inconnu');
				}
			} else {
				throw new ErrorException('cette base ne vous appartient pas');
			}
		} else {
			throw new ErrorException('erreur système');
		}
	} else {
		throw new ErrorException('envoi de vaisseau impossible');
	}
} else {
	throw new FormException('pas assez d\'informations pour envoyer des vaisseaux');
}