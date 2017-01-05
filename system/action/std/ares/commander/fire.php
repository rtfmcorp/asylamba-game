<?php

# fire a commander

# int id 	 		id du commandant

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Library\Flashbag;

$response = $this->getContainer()->get('app.response');

$commanderId = $this->getContainer()->get('app.request')->query->get('id');
if ($commanderId === null) {
	throw new ErrorException('manque d\'information pour le traitement de la requête');
}

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$session = $this->getContainer()->get('app.session');

$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commanderId, 'c.rPlayer' => $session->get('playerId')));

if ($commanderManager->size() !== 1) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas.');
}
$commander = $commanderManager->get();

if ($commander->statement == 1) {

	// vider le commandant
	$commanderManager->emptySquadrons($commander);
	$commander->setStatement(4);

	$session->addFlashbag('Vous avez renvoyé votre commandant ' . $commander->getName() . '.', Flashbag::TYPE_SUCCESS);
} else {
	$session->addFlashbag('Vous ne pouvez pas renvoyer un officier en déplacement.', Flashbag::TYPE_SUCCESS);
}

$commanderManager->changeSession($S_COM1);

$response->redirect('fleet');