<?php
# dequeue ship action

# int baseId 		id (rPlace) de la base orbitale
# int typeofship 	type de vaisseau
# int quantity 			nombre de vaisseaux à recycler

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Athena\Resource\ShipResource;

$baseId = Utils::getHTTPData('baseid');
$typeOfShip = Utils::getHTTPData('typeofship');
$quantity = Utils::getHTTPData('quantity');

if ($baseId !== FALSE AND $typeOfShip !== FALSE AND $quantity !== FALSE) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

		if (ASM::$obm->size() > 0) {
			$ob = ASM::$obm->get();
			if ($quantity > 0 && $quantity <= $ob->shipStorage[$typeOfShip]) {
				$resources = ($quantity * ShipResource::getInfo($typeOfShip, 'resourcePrice')) / 2;
				$ob->shipStorage[$typeOfShip] -= $quantity;
				$ob->increaseResources($resources);
			} else {
				CTR::$alert->add('cette quantité ne correspond pas à votre stock', ALERT_STD_ERROR);	
			}
		} else {
			CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);	
		}
		ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations', ALERT_STD_FILLFORM);
}
