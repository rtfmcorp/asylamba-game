<?php
# dequeue ship action

# int baseId 		id (rPlace) de la base orbitale
# int typeofship 	type de vaisseau
# int quantity 			nombre de vaisseaux à recycler

use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

$baseId = $request->query->get('baseid');
$typeOfShip = $request->query->get('typeofship');
$quantity = floatval($request->request->get('quantity'));

if ($baseId !== false and $typeOfShip !== false and $quantity !== false) {
    
    if (intval($quantity) != $quantity) {
        throw new ErrorException('la quantité doit être un nombre entier');
    }
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
$this->getContainer()->get('entity_manager')->flush($ob);
