<?php

# fire a commander

# int id 	 		id du commandant

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Library\Http\Response;

if (($commanderId = $this->getContainer()->get('app.request')->request->get('id')) === null) {
	throw new ErrorException('manque d\'information pour le traitement de la requête');
}

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$session = $this->getContainer()->get('app.session');
$response = $this->getContainer()->get('app.response');

$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commanderId, 'c.rPlayer' => $session->get('playerId')));

if ($commanderManager->size() !== 1) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas.');
}
$commander = $commanderManager->get();

if ($commander->statement == 1) {

	// vider le commandant
	$commander->emptySquadrons();
	$commander->setStatement(4);

	$response->flashbag->add('Vous avez renvoyé votre commandant ' . $commander->getName() . '.', Response::FLASHBAG_SUCCESS);
} else {
	$response->flashbag->add('Vous ne pouvez pas renvoyer un officier en déplacement.', Response::FLASHBAG_SUCCESS);
}

$commanderManager->changeSession($S_COM1);