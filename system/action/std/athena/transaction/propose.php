<?php
include_once ATHENA;
# propose a transaction action

# int rPlace 		id de la base orbitale
# int type 			type of transaction
# [int quantity] 	quantitiy of ships or resources
# [int identifier]	rCommander or shipId
# int price 		price defined by the proposer

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$rPlace = Utils::getHTTPData('rPlace');
$type = Utils::getHTTPData('type');
$quantity = Utils::getHTTPData('quantity');
$identifier = Utils::getHTTPData('identifier');
$price = Utils::getHTTPData('price');

if ($rPlace !== FALSE AND $type !== FALSE AND $price !== FALSE AND in_array($rPlace, $verif)) {
	$valid = TRUE;
	switch ($type) {
		case Transaction::TYP_RESOURCE :
			if ($quantity !== FALSE AND $quantity > 0) {
				$identifier = 0;
			} else {
				$valid = FALSE;
			}
			break;
		case Transaction::TYP_SHIP :
			if ($identifier !== FALSE AND ShipResource::isAShip($identifier)) {
				if (ShipResource::isAShipFromDock1($identifier)) {
					if ($quantity === FALSE) {
						$quantity = 1;
					} else {
						if ($quantity < 1) {
							$valid = FALSE;
						}
					}
				} else {
					$quantity = 1;
				}
			} else {
				$valid = FALSE;
			}
			break;
		case Transaction::TYP_COMMANDER :
			if ($identifier === FALSE OR $identifier < 1) {
				$valid = FALSE;
			} else {
				$quantity = 1;
			}
			break;
		default :
			$valid = FALSE;
	}
	if ($valid) {
		$valid = TRUE;

		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $rPlace));
		$base = ASM::$obm->get();

		switch ($type) {
			case Transaction::TYP_RESOURCE :
				if ($base->getResourcesStorage() >= $quantity) {
					$commercialShipQuantity = Game::getCommercialShipQuantityNeeded($type, $quantity);
					$base->decreaseResources($quantity);
				} else {
					$valid = FALSE;
				}
				break;
			case Transaction::TYP_SHIP :
				$inStorage = $base->getShipStorage($identifier);
				if ($inStorage >= $quantity) {
					$commercialShipQuantity = Game::getCommercialShipQuantityNeeded($type, $quantity, $identifier);
					$base->setShipStorage($identifier, $inStorage - $quantity);
				} else {
					$valid = FALSE;
				}
				break;
			case Transaction::TYP_COMMANDER :
				$valie = FALSE; // provisoirement
				/*$S_COM1 = ASM::$com->getCurrentSession();
				ASM::$com->newSession(ASM_UMODE);
				ASM::$com->load(array('id' => $identifier));
				$commander = ASM::$com->get();

				$commercialShipQuantity = Game::getCommercialShipQuantityNeeded($type, $quantity);

				ASM::$com->changeSession($S_COM1);*/
				break;
		}

		if ($valid) {
			// création de la transaction
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

			// création du convoi
			$cs = new CommercialShipping();
			$cs->rPlayer = CTR::$data->get('playerId');
			$cs->rBase = $rPlace;
			$cs->rBaseDestination = 0;
			$cs->rTransaction = $tr->id;
			$cs->resourceTransported = NULL;
			$cs->shipQuantity = $commercialShipQuantity;
			$cs->dDeparture = '';
			$cs->dArrival = '';
			$cs->statement = CommercialShipping::ST_WAITING;
			ASM::$csm->add($cs);

			CTR::$alert->add('Votre proposition a été envoyée sur le marché.', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('impossible de faire une proposition sur le marché !', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('impossible de faire une proposition sur le marché', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour faire une proposition sur le marché', ALERT_STD_FILLFORM);
}
?>