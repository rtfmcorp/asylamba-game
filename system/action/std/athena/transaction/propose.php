<?php
# propose a transaction action

# int rplace 		id de la base orbitale
# int type 			type of transaction
# [int quantity] 	quantitiy of resources or ships
# [int identifier]	rCommander or shipId
# int price 		price defined by the proposer

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Ares\Model\Commander;

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$rPlace = Utils::getHTTPData('rplace');
$type = Utils::getHTTPData('type');
$quantity = Utils::getHTTPData('quantity');
$identifier = Utils::getHTTPData('identifier');
$price = Utils::getHTTPData('price');

if ($rPlace !== FALSE AND $type !== FALSE AND $price !== FALSE AND in_array($rPlace, $verif)) {
	$valid = TRUE;

	switch ($type) {
		case Transaction::TYP_RESOURCE :
			if ($quantity !== FALSE AND intval($quantity) > 0) {
				$identifier = 0;
			} else {
				$valid = FALSE;
			}
			break;
		case Transaction::TYP_SHIP :
			if ($identifier !== FALSE AND ShipResource::isAShip($identifier)) {
				if (ShipResource::isAShipFromDock1($identifier) OR ShipResource::isAShipFromDock2($identifier)) {
					if ($quantity === FALSE) {
						$quantity = 1;
					} else {
						if (intval($quantity) < 1) {
							$valid = FALSE;
						}
					}
				} else {
					$valid = FALSE;
				}
			} else {
				$valid = FALSE;
			}
			break;
		case Transaction::TYP_COMMANDER :
			if ($identifier === FALSE OR $identifier < 1) {
				$valid = FALSE;
			}
			break;
		default :
			$valid = FALSE;
	}
	if ($valid) {
		$minPrice = Game::getMinPriceRelativeToRate($type, $quantity, $identifier);
		$maxPrice = Game::getMaxPriceRelativeToRate($type, $quantity, $identifier);

		if ($price < $minPrice) {
			CTR::$alert->add('Le prix que vous avez fixé est trop bas. Une limite inférieure est fixée selon la catégorie de la vente.', ALERT_STD_ERROR);
		} elseif ($price > $maxPrice) {
			CTR::$alert->add('Le prix que vous avez fixé est trop haut. Une limite supérieure est fixée selon la catégorie de la vente.', ALERT_STD_ERROR);
		} else {
			$valid = TRUE;

			$S_OBM1 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession(ASM_UMODE);
			ASM::$obm->load(array('rPlace' => $rPlace));
			$base = ASM::$obm->get();

			if ($valid) {
				# verif : have we enough commercialShips
				$totalShips = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::COMMERCIAL_PLATEFORME, 'level', $base->getLevelCommercialPlateforme(), 'nbCommercialShip');
				$usedShips = 0;

				$S_CSM1 = ASM::$csm->getCurrentSession();
				ASM::$csm->changeSession($base->shippingManager);
				for ($i = 0; $i < ASM::$csm->size(); $i++) { 
					if (ASM::$csm->get($i)->rBase == $rPlace) {
						$usedShips += ASM::$csm->get($i)->shipQuantity;
					}
				}

				# determine commercialShipQuantity needed
				switch ($type) {
					case Transaction::TYP_RESOURCE :
						if ($base->getResourcesStorage() >= $quantity) {
							$commercialShipQuantity = Game::getCommercialShipQuantityNeeded($type, $quantity);
						} else {
							$valid = FALSE;
						}
						break;
					case Transaction::TYP_SHIP :
						$inStorage = $base->getShipStorage($identifier);
						if ($inStorage >= $quantity) {
							$commercialShipQuantity = Game::getCommercialShipQuantityNeeded($type, $quantity, $identifier);
						} else {
							$valid = FALSE;
						}
						break;
					case Transaction::TYP_COMMANDER :
						$commercialShipQuantity = Game::getCommercialShipQuantityNeeded($type, $quantity);
						break;
				}

				$remainingShips = $totalShips - $usedShips;
				if ($valid) {
					if ($remainingShips >= $commercialShipQuantity) {
						switch ($type) {
							case Transaction::TYP_RESOURCE :
								$base->decreaseResources($quantity);
								break;
							case Transaction::TYP_SHIP :
								$inStorage = $base->getShipStorage($identifier);
								$base->setShipStorage($identifier, $inStorage - $quantity);
								break;
							case Transaction::TYP_COMMANDER :
								$S_COM1 = ASM::$com->getCurrentSession();
								ASM::$com->newSession();
								ASM::$com->load(array('c.id' => $identifier));
								if (ASM::$com->size() == 1 AND ASM::$com->get()->getRPlayer() == CTR::$data->get('playerId') AND ASM::$com->get()->statement !== Commander::ONSALE) {
									$commander = ASM::$com->get();
									$commander->statement = Commander::ONSALE;
									$commander->emptySquadrons();
								} else {
									$valid = FALSE;
								}
								ASM::$com->changeSession($S_COM1);
								
								break;
						}

						if ($valid) {
							# création de la transaction
							$tr = new Transaction();
							$tr->rPlayer = CTR::$data->get('playerId');
							$tr->rPlace = $rPlace;
							$tr->type = $type; 
							$tr->quantity = $quantity;
							$tr->identifier = $identifier;
							$tr->price = $price;
							$tr->commercialShipQuantity = $commercialShipQuantity;
							$tr->statement = Transaction::ST_PROPOSED;
							$tr->dPublication = Utils::now();
							ASM::$trm->add($tr);

							# création du convoi
							$cs = new CommercialShipping();
							$cs->rPlayer = CTR::$data->get('playerId');
							$cs->rBase = $rPlace;
							$cs->rBaseDestination = 0;
							$cs->rTransaction = $tr->id;
							$cs->resourceTransported = NULL;
							$cs->shipQuantity = $commercialShipQuantity;
							$cs->dDeparture = NULL;
							$cs->dArrival = NULL;
							$cs->statement = CommercialShipping::ST_WAITING;
							ASM::$csm->add($cs);

							CTR::$alert->add('Votre proposition a été envoyée sur le marché.', ALERT_GAM_MARKET);
						} else {
							CTR::$alert->add('Il y a un problème avec votre commandant.', ALERT_STD_ERROR);
						}
					} else {
						CTR::$alert->add('Vous n\'avez pas assez de vaisseaux de transport disponibles.', ALERT_STD_ERROR);
					}
				} else {
					switch ($type) {
						case Transaction::TYP_RESOURCE :
							CTR::$alert->add('Vous n\'avez pas assez de ressources en stock.', ALERT_STD_ERROR);
							break;
						case Transaction::TYP_SHIP :
							CTR::$alert->add('Vous n\'avez pas assez de vaisseaux.', ALERT_STD_ERROR);
							break;
						default:
							CTR::$alert->add('Erreur pour une raison étrange, contactez un administrateur.', ALERT_STD_ERROR);
					}
				}
				ASM::$csm->changeSession($S_CSM1);
			} else {
				CTR::$alert->add('impossible de faire une proposition sur le marché !', ALERT_STD_ERROR);
			}
			ASM::$obm->changeSession($S_OBM1);
		}
	} else {
		CTR::$alert->add('impossible de faire une proposition sur le marché', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour faire une proposition sur le marché', ALERT_STD_FILLFORM);
}
