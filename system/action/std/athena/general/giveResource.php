<?php
# give resources action

# int baseid 		id (rPlace) de la base orbitale
# int otherbaseid 	id (rPlace) de la base orbitale à qui on veut envoyer des ressources
# int quantity 		quantité de ressources à envoyer

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Hermes\Model\Notification;

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$otherBaseId = Utils::getHTTPData('otherbaseid');
$quantity = Utils::getHTTPData('quantity');

if ($baseId !== FALSE AND $otherBaseId !== FALSE AND $quantity !== FALSE AND in_array($baseId, $verif)) {
	if ($baseId != $otherBaseId) {

		$resource = intval($quantity);

		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

		if (ASM::$obm->size() > 0) {
			$orbitalBase = ASM::$obm->get();

			if ($resource > 0) {
				if ($orbitalBase->getResourcesStorage() >= $resource) {
					//---------------------------
					# controler le nombre de vaisseaux
					# verif : have we enough commercialShips
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
					$commercialShipQuantity = Game::getCommercialShipQuantityNeeded(Transaction::TYP_RESOURCE, $resource);

					if ($remainingShips >= $commercialShipQuantity) {
						
						ASM::$obm->load(array('rPlace' => $otherBaseId));
						if (ASM::$obm->size() == 2) {
							$otherBase = ASM::$obm->get(1);

							# load places to compute travel time
							$S_PLM1 = ASM::$plm->getCurrentSession();
							ASM::$plm->newSession(ASM_UMODE);
							ASM::$plm->load(array('id' => $orbitalBase->rPlace));
							ASM::$plm->load(array('id' => $otherBase->rPlace));
							$timeToTravel = Game::getTimeToTravelCommercial(ASM::$plm->get(0), ASM::$plm->get(1));
							$departure = Utils::now();
							$arrival = Utils::addSecondsToDate($departure, $timeToTravel);
							ASM::$plm->changeSession($S_PLM1);

							# création du convoi
							$cs = new CommercialShipping();
							$cs->rPlayer = CTR::$data->get('playerId');
							$cs->rBase = $orbitalBase->rPlace;
							$cs->rBaseDestination = $otherBase->rPlace;
							$cs->resourceTransported = $resource;
							$cs->shipQuantity = $commercialShipQuantity;
							$cs->dDeparture = $departure;
							$cs->dArrival = $arrival;
							$cs->statement = CommercialShipping::ST_GOING;
							ASM::$csm->add($cs);

							$orbitalBase->decreaseResources($resource);

							if ($orbitalBase->getRPlayer() != $otherBase->getRPlayer()) {
								$n = new Notification();
								$n->setRPlayer($otherBase->getRPlayer());
								$n->setTitle('Envoi de ressources');
								$n->addBeg()->addTxt($otherBase->getName())->addSep();
								$n->addLnk('embassy/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'));
								$n->addTxt(' a lancé un convoi de ')->addStg(Format::numberFormat($resource))->addTxt(' ressources depuis sa base ');
								$n->addLnk('map/place-' . $orbitalBase->getRPlace(), $orbitalBase->getName())->addTxt('. ');
								$n->addBrk()->addTxt('Quand le convoi arrivera, les ressources seront à vous.');
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
								$qr->execute([CTR::$data->get('playerId'), $otherBase->getRPlayer(), 4, DataAnalysis::resourceToStdUnit($resource), Utils::now()]);
							}

							CTR::$alert->add('Ressources envoyées', ALERT_STD_SUCCESS);
						} else {
							CTR::$alert->add('envoi de ressources impossible - erreur dans les bases orbitales', ALERT_STD_ERROR);
						}
					} else {
						CTR::$alert->add('envoi de ressources impossible - vous n\'avez pas assez de vaisseaux de transport', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('envoi de ressources impossible - vous ne pouvez pas envoyer plus que ce que vous possédez', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('envoi de ressources impossible - il faut envoyer un nombre entier positif', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('envoi de ressources impossible - action inutile, vous ressources sont déjà sur cette base orbitale', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour envoyer des ressources', ALERT_STD_FILLFORM);
}