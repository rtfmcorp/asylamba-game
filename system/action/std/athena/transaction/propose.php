<?php
# propose a transaction action

# int rplace 		id de la base orbitale
# int type 			type of transaction
# [int quantity] 	quantitiy of resources or ships
# [int identifier]	rCommander or shipId
# int price 		price defined by the proposer

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$commercialShippingManager = $this->getContainer()->get('athena.commercial_shipping_manager');
$transactionManager = $this->getContainer()->get('athena.transaction_manager');
$entityManager = $this->getContainer()->get('entity_manager');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$rPlace = $request->query->get('rplace');
$type = $request->query->get('type');
$quantity = $request->request->get('quantity');
$identifier = $request->query->get('identifier');
$price = $request->request->get('price');

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
			throw new ErrorException('Le prix que vous avez fixé est trop bas. Une limite inférieure est fixée selon la catégorie de la vente.');
		} elseif ($price > $maxPrice) {
			throw new ErrorException('Le prix que vous avez fixé est trop haut. Une limite supérieure est fixée selon la catégorie de la vente.');
		} else {
			$valid = TRUE;
			$base = $orbitalBaseManager->get($rPlace);

			if ($valid) {
				# verif : have we enough commercialShips
				$totalShips = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::COMMERCIAL_PLATEFORME, 'level', $base->getLevelCommercialPlateforme(), 'nbCommercialShip');
				$usedShips = 0;

				foreach ($base->commercialShippings as $commercialShipping) { 
					if ($commercialShipping->rBase == $rPlace) {
						$usedShips += $commercialShipping->shipQuantity;
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
								$orbitalBaseManager->decreaseResources($base, $quantity);
								break;
							case Transaction::TYP_SHIP :
								$inStorage = $base->getShipStorage($identifier);
								$base->setShipStorage($identifier, $inStorage - $quantity);
								break;
							case Transaction::TYP_COMMANDER :
								if (($commander = $commanderManager->get($identifier)) !== null AND $commander->getRPlayer() == $session->get('playerId') AND $commander->statement !== Commander::ONSALE) {
									$commander->statement = Commander::ONSALE;
									$commanderManager->emptySquadrons($commander);
								} else {
									$valid = FALSE;
								}
								break;
						}

						if ($valid) {
							# création de la transaction
							$tr = new Transaction();
							$tr->rPlayer = $session->get('playerId');
							$tr->rPlace = $rPlace;
							$tr->type = $type; 
							$tr->quantity = $quantity;
							$tr->identifier = $identifier;
							$tr->price = $price;
							$tr->commercialShipQuantity = $commercialShipQuantity;
							$tr->statement = Transaction::ST_PROPOSED;
							$tr->dPublication = Utils::now();
							$transactionManager->add($tr);

							# création du convoi
							$cs = new CommercialShipping();
							$cs->rPlayer = $session->get('playerId');
							$cs->rBase = $rPlace;
							$cs->rBaseDestination = 0;
							$cs->rTransaction = $tr->id;
							$cs->resourceTransported = NULL;
							$cs->shipQuantity = $commercialShipQuantity;
							$cs->dDeparture = NULL;
							$cs->dArrival = NULL;
							$cs->statement = CommercialShipping::ST_WAITING;
							$commercialShippingManager->add($cs);

							$session->addFlashbag('Votre proposition a été envoyée sur le marché.', Flashbag::TYPE_MARKET_SUCCESS);
						} else {
							throw new ErrorException('Il y a un problème avec votre commandant.');
						}
					} else {
						throw new ErrorException('Vous n\'avez pas assez de vaisseaux de transport disponibles.');
					}
				} else {
					switch ($type) {
						case Transaction::TYP_RESOURCE :
							throw new ErrorException('Vous n\'avez pas assez de ressources en stock.');
						case Transaction::TYP_SHIP :
							throw new ErrorException('Vous n\'avez pas assez de vaisseaux.');
						default:
							throw new ErrorException('Erreur pour une raison étrange, contactez un administrateur.');
					}
				}
			} else {
				throw new ErrorException('impossible de faire une proposition sur le marché !');
			}
		}
	} else {
		throw new ErrorException('impossible de faire une proposition sur le marché');
	}
} else {
	throw new FormException('pas assez d\'informations pour faire une proposition sur le marché');
}
$entityManager->flush();