<?php
# give resources action

# int baseid 		id (rPlace) de la base orbitale
# int otherbaseid 	id (rPlace) de la base orbitale à qui on veut envoyer des ressources
# int quantity 		quantité de ressources à envoyer
# [int identifier]	shipId

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Classes\Database\Database;

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$otherBaseId = Utils::getHTTPData('otherbaseid');

if ($baseId !== FALSE AND $otherBaseId !== FALSE AND in_array($baseId, $verif)) {
	if ($baseId != $otherBaseId) {
		for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) { 
			if (CTR::$post->exist('identifier-' . $i)) {
				$shipType = $i;
				$shipName = ShipResource::getInfo($i, 'codeName');

				if (CTR::$post->exist('quantity-' . $i)) {
					$ships = CTR::$post->get('quantity-' . $i) > 0
						? CTR::$post->get('quantity-' . $i) : 1;
					$ships = intval($ships);
				}

				break;
			}
		}

		if (isset($shipType) AND isset($ships)) {
			$S_OBM1 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession(ASM_UMODE);
			ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

			if (ASM::$obm->size() > 0) {
				$orbitalBase = ASM::$obm->get();

				if (ShipResource::isAShipFromDock1($shipType) OR ShipResource::isAShipFromDock2($shipType)) {
					if ($ships > 0) {
						if ($orbitalBase->getShipStorage($shipType) >= $ships) {
							$commercialShipQuantity = Game::getCommercialShipQuantityNeeded(Transaction::TYP_SHIP, $ships, $shipType);
							$totalShips = OrbitalBaseResource::getBuildingInfo(6, 'level', $orbitalBase->getLevelCommercialPlateforme(), 'nbCommercialShip');
							$usedShips = 0;

							$S_CSM1 = ASM::$csm->getCurrentSession();
							ASM::$csm->changeSession($orbitalBase->shippingManager);
							for ($i = 0; $i < ASM::$csm->size(); $i++) { 
								if (ASM::$csm->get($i)->rBase == $orbitalBase->rPlace) {
									$usedShips += ASM::$csm->get($i)->shipQuantity;
								}
							}
							ASM::$csm->changeSession($S_CSM1);

							$remainingShips = $totalShips - $usedShips;

							if ($remainingShips >= $commercialShipQuantity) {
								ASM::$obm->load(array('rPlace' => $otherBaseId));
								
								if (ASM::$obm->size() == 2) {
									$otherBase = ASM::$obm->get(1);

									# load places to compute travel time
									$S_PLM1 = ASM::$plm->getCurrentSession();
									ASM::$plm->newSession();
									ASM::$plm->load(array('id' => $orbitalBase->rPlace));
									ASM::$plm->load(array('id' => $otherBase->rPlace));
									
									$timeToTravel = Game::getTimeToTravelCommercial(ASM::$plm->get(0), ASM::$plm->get(1));
									$departure = Utils::now();
									$arrival = Utils::addSecondsToDate($departure, $timeToTravel);
									
									ASM::$plm->changeSession($S_PLM1);

									# création de la transaction
									$tr = new Transaction();
									$tr->rPlayer = CTR::$data->get('playerId');
									$tr->rPlace = $orbitalBase->rPlace;
									$tr->type = Transaction::TYP_SHIP; 
									$tr->quantity = $ships;
									$tr->identifier = $shipType;
									$tr->price = 0;
									$tr->commercialShipQuantity = $commercialShipQuantity;
									$tr->statement = Transaction::ST_COMPLETED;
									$tr->dPublication = Utils::now();
									ASM::$trm->add($tr);

									# création du convoi
									$cs = new CommercialShipping();
									$cs->rPlayer = CTR::$data->get('playerId');
									$cs->rBase = $orbitalBase->rPlace;
									$cs->rBaseDestination = $otherBase->rPlace;
									$cs->rTransaction = $tr->id;
									$cs->resourceTransported = 0;
									$cs->shipQuantity = $commercialShipQuantity;
									$cs->dDeparture = $departure;
									$cs->dArrival = $arrival;
									$cs->statement = CommercialShipping::ST_GOING;
									ASM::$csm->add($cs);

									$orbitalBase->setShipStorage($shipType, $orbitalBase->getShipStorage($shipType) - $ships);

									if ($orbitalBase->getRPlayer() != $otherBase->getRPlayer()) {
										$n = new Notification();
										$n->setRPlayer($otherBase->getRPlayer());
										$n->setTitle('Envoi de vaisseaux');
										$n->addBeg()->addTxt($otherBase->getName())->addSep();
										$n->addLnk('embassy/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'));
										$n->addTxt(' a lancé un convoi de ')->addStg(Format::numberFormat($ships))->addTxt(' ' . $shipName . ' depuis sa base ');
										$n->addLnk('map/place-' . $orbitalBase->getRPlace(), $orbitalBase->getName())->addTxt('. ');
										$n->addBrk()->addTxt('Quand le convoi arrivera, les vaisseaux seront placés dans votre hangar.');
										$n->addSep()->addLnk('bases/base-' . $otherBase->getId()  . '/view-commercialplateforme/mode-market', 'vers la place du commerce →');
										$n->addEnd();
										
										ASM::$ntm->add($n);
									}

									if (DATA_ANALYSIS) {
										$db = Database::getInstance();
										$qr = $db->prepare('INSERT INTO 
											DA_CommercialRelation(`from`, `to`, type, weight, dAction)
											VALUES(?, ?, ?, ?, ?)'
										);
										$qr->execute([CTR::$data->get('playerId'), $otherBase->getRPlayer(), 3, DataAnalysis::resourceToStdUnit(ShipResource::getInfo($shipType, 'resourcePrice') * $ships), Utils::now()]);
									}
									CTR::$alert->add('Vaisseaux envoyés', ALERT_STD_SUCCESS);
								} else {
									CTR::$alert->add('Erreur dans les bases orbitales', ALERT_STD_ERROR);
								}
							} else {
								CTR::$alert->add('Vous n\'avez pas assez de vaisseaux de transport', ALERT_STD_ERROR);
							}
						} else {
							CTR::$alert->add('Vous n\'avez pas assez de vaisseaux de ce type', ALERT_STD_ERROR);
						}
					} else {
						CTR::$alert->add('Envoi de vaisseaux impossible', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('Vaisseau inconnu', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
			}
			ASM::$obm->changeSession($S_OBM1);
		} else {
			CTR::$alert->add('erreur système', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('envoi de vaisseau impossible', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour envoyer des vaisseaux', ALERT_STD_FILLFORM);
}