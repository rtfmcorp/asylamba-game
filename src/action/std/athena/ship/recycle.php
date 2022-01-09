<?php
# dequeue ship action

# int baseId 		id (rPlace) de la base orbitale
# int typeofship 	type de vaisseau
# int quantity 			nombre de vaisseaux à recycler

use App\Modules\Athena\Resource\ShipResource;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$orbitalBaseManager = $this->getContainer()->get(\App\Modules\Athena\Manager\OrbitalBaseManager::class);

$baseId = $request->query->get('baseid');
$typeOfShip = $request->query->get('typeofship');
$quantity = $request->request->get('quantity');

if ($baseId !== FALSE AND $typeOfShip !== FALSE AND $quantity !== FALSE) {
		if (($ob = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId')))) {
			if ($quantity > 0 && $quantity <= $ob->shipStorage[$typeOfShip]) {
				$resources = ($quantity * ShipResource::getInfo($typeOfShip, 'resourcePrice')) / 2;
				$ob->shipStorage[$typeOfShip] -= $quantity;
				$orbitalBaseManager->increaseResources($ob, $resources);
			} else {
				throw new ErrorException('cette quantité ne correspond pas à votre stock');	
			}
		} else {
			throw new ErrorException('cette base ne vous appartient pas');	
		}
} else {
	throw new FormException('pas assez d\'informations');
}
$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush($ob);
