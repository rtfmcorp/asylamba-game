<?php

# empty a commander

# int id 	 		id du commandant

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;

if (($commanderId = $this->getContainer()->get('app.request')->request->get('id')) === null) {
	throw new ErrorException('manque d\'information pour le traitement de la requête');
}
$commanderManager = $this->getContainer()->get('ares.commander_manager');

$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commanderId, 'c.rPlayer' => $this->getContainer()->get('app.session')->get('playerId')));

if ($commanderManager->size() !== 1) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas.');
}
$commander = $commanderManager->get();
if ($commander->statement !== 1) {
	throw new ErrorException('Vous ne pouvez pas retirer les vaisseaux à un officier en déplacement.');
}

// vider le commandant
$commander->emptySquadrons();

$this->getContainer()->get('app.response')->flashbag->add('Vous avez vidé l\'armée menée par votre commandant ' . $commander->getName() . '.', Response::FLASHBAG_SUCCESS);
$commanderManager->changeSession($S_COM1);