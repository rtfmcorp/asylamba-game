<?php
# assign ship action

# string direction 	'ctb' = commander to base / 'btc' = base to commander
					# [commander] envoie [quantity] [ship] depuis son [squadron] a une [base]
					# [base] envoie [quantity] [ship] a un [commander] sur son [squadron]
# int base 			base id
# int ship  		ship id
# int quantity		ship quantity
# int commander		commander id
# int squadron 		squadron id

use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Zeus\Resource\TutorialResource;

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
    $verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$quantity = $request->request->get('quantity');

if (
    ($direction = $request->request->get('direction')) === null ||
    ($baseId = $request->request->get('base')) === null ||
    ($shipId = $request->request->get('ship')) === null ||
    ($commanderId = $request->request->get('commander')) === null ||
    ($squadron = $request->request->get('squadron')) === null ||
    !in_array($baseId, $verif)
) {
    throw new FormException('Pas assez d\'informations pour assigner un vaisseau');
}
if (!in_array($direction, ['ctb', 'btc'])) {
    throw new FormException('L\'argument direction n\'est pas correct.');
}
if (!ShipResource::isAShip($shipId)) {
    throw new FormException('Le vaisseau n\'existe pas.');
}
if ($quantity === null) {
    $quantity = 1;
}

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

$S_OBM1 = $orbitalBaseManager->getCurrentSession();
$orbitalBaseManager->newSession();
$orbitalBaseManager->load(array('rPlace' => $baseId));

$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commanderId, 'c.rBase' => $baseId));

if ($orbitalBaseManager->size() !== 1 || $commanderManager->size() !== 1) {
    throw new ErrorException('Erreur dans les commandants ou la base.');
}

$base = $orbitalBaseManager->get();
$commander = $commanderManager->get();

if ($commander->statement !== Commander::AFFECTED) {
    throw new ErrorException('Cet officier ne peut être modifié.');	
}

if ($direction == 'ctb') { // commander to base
    // if the commander has the quantity of ships required
    if ($commander->getSquadron($squadron)->getNbrShipByType($shipId) - $quantity >= 0) {
            $base->setShipStorage($shipId, ($base->getShipStorage($shipId) + $quantity));
            $commander->getSquadron($squadron)->updateShip($shipId, -$quantity);
            # $alert->add('Vaisseau(x) envoyé(s) à la base.', ALERT_BUG_SUCCESS);
    } else {
            throw new ErrorException('L\'escadrille n\'a pas autant de vaisseaux !');
    }
} else {							// base to commander
    // if the base has the quantity of ships required
    if ($base->getShipStorage($shipId) - $quantity < 0) {
        throw new ErrorException('La base n\'a pas autant de vaisseaux !');
    }
    // if it's enough PEV space in the commander
    if (($commander->getSquadron($squadron)->getPev() + (ShipResource::getInfo($shipId, 'pev') * $quantity)) > 100) {
        throw new ErrorException('Il n\'y a pas assez de place dans l\'escadrille pour ces vaisseaux.');
    }
    $base->setShipStorage($shipId, ($base->getShipStorage($shipId) - $quantity));
    $commander->getSquadron($squadron)->updateShip($shipId, $quantity);
    # $alert->add('Vaisseau(x) envoyé(s) dans l\'escadrille.', ALERT_BUG_SUCCESS);

    # tutorial
    if ($session->get('playerInfo')->get('stepDone') == false && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::FILL_SQUADRON) {
        TutorialHelper::setStepDone();
    }
}
$commanderManager->changeSession($S_COM1);
$orbitalBaseManager->changeSession($S_OBM1);