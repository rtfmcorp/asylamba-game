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
$session = $this->getContainer()->get('session_wrapper');

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $session->get('playerId')) {
    throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas.');
}

if ($commander->statement == 1) {

    // vider le commandant
    $commanderManager->emptySquadrons($commander);
    $commander->setStatement(4);

    $session->addFlashbag('Vous avez renvoyé votre commandant ' . $commander->getName() . '.', Flashbag::TYPE_SUCCESS);
} else {
    $session->addFlashbag('Vous ne pouvez pas renvoyer un officier en déplacement.', Flashbag::TYPE_SUCCESS);
}

$this->getContainer()->get('entity_manager')->flush();

$response->redirect('fleet');
