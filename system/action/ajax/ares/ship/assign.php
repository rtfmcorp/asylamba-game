<?php
# assign ship action

# string direction 	'ctb' = commander to orbitalBase / 'btc' = orbitalBase to commander
					# [commander] envoie [quantity] [ship] depuis son [squadron] a une [orbitalBase]
					# [orbitalBase] envoie [quantity] [ship] a un [commander] sur son [squadron]
# int orbitalBase 			orbitalBase id
# int ship  		ship id
# int quantity		ship quantity
# int commander		commander id
# int squadron 		squadron id

use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Zeus\Resource\TutorialResource;

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$tutorialHelper = $this->getContainer()->get(\Asylamba\Modules\Zeus\Helper\TutorialHelper::class);

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
    $verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$quantity = $request->request->get('quantity');

if (
    ($direction = $request->request->get('direction')) === null ||
    ($orbitalBaseId = $request->request->get('orbitalBase')) === null ||
    ($shipId = $request->request->get('ship')) === null ||
    ($commanderId = $request->request->get('commander')) === null ||
    ($squadron = $request->request->get('squadron')) === null ||
    !in_array($orbitalBaseId, $verif)
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

$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_orbitalBase_manager');

if (
	($orbitalBase = $orbitalBaseManager->get($orbitalBaseId)) === null ||
	($commander = $commanderManager->get($commanderId)) === null ||
	$commander->rBase !== $orbitalBaseId
) {
    throw new ErrorException('Erreur dans les commandants ou la base.');
}

if ($commander->statement !== Commander::AFFECTED) {
    throw new ErrorException('Cet officier ne peut être modifié.');	
}

if ($direction == 'ctb') { // commander to orbitalBase
    // if the commander has the quantity of ships required
    if ($commander->getSquadron($squadron)->getNbrShipByType($shipId) - $quantity >= 0) {
            $orbitalBase->setShipStorage($shipId, ($orbitalBase->getShipStorage($shipId) + $quantity));
            $commander->getSquadron($squadron)->updateShip($shipId, -$quantity);
            # $alert->add('Vaisseau(x) envoyé(s) à la orbitalBase.', ALERT_BUG_SUCCESS);
    } else {
            throw new ErrorException('L\'escadrille n\'a pas autant de vaisseaux !');
    }
} else {							// orbitalBase to commander
    // if the orbitalBase has the quantity of ships required
    if ($orbitalBase->getShipStorage($shipId) - $quantity < 0) {
        throw new ErrorException('La base n\'a pas autant de vaisseaux !');
    }
    // if it's enough PEV space in the commander
    if (($commander->getSquadron($squadron)->getPev() + (ShipResource::getInfo($shipId, 'pev') * $quantity)) > 100) {
        throw new ErrorException('Il n\'y a pas assez de place dans l\'escadrille pour ces vaisseaux.');
    }
    $orbitalBase->setShipStorage($shipId, ($orbitalBase->getShipStorage($shipId) - $quantity));
    $commander->getSquadron($squadron)->updateShip($shipId, $quantity);
    # $alert->add('Vaisseau(x) envoyé(s) dans l\'escadrille.', ALERT_BUG_SUCCESS);

    # tutorial
    if ($session->get('playerInfo')->get('stepDone') == false && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::FILL_SQUADRON) {
        $tutorialHelper->setStepDone();
    }
}
