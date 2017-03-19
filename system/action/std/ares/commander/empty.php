<?php

# empty a commander

# int id 	 		id du commandant

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;

$commanderId = $this->getContainer()->get('app.request')->query->get('id');
if ($commanderId === null) {
	throw new ErrorException('manque d\'information pour le traitement de la requête');
}
$commanderManager = $this->getContainer()->get('ares.commander_manager');

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $this->getContainer()->get('app.session')->get('playerId')) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas.');
}
if ($commander->statement !== 1) {
	throw new ErrorException('Vous ne pouvez pas retirer les vaisseaux à un officier en déplacement.');
}

// vider le commandant
$commanderManager->emptySquadrons($commander);

$this->getContainer()->get('app.session')->addFlashbag('Vous avez vidé l\'armée menée par votre commandant ' . $commander->getName() . '.', Flashbag::TYPE_SUCCESS);

$this->getContainer()->get('entity_manager')->flush();