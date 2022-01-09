<?php

# fire a commander

# int id 	 		id du commandant

use App\Classes\Exception\ErrorException;
use App\Classes\Library\Flashbag;

$response = $this->getContainer()->get('app.response');

$commanderId = $this->getContainer()->get('app.request')->query->get('id');
if ($commanderId === null) {
	throw new ErrorException('manque d\'information pour le traitement de la requête');
}

$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

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

$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush();

$response->redirect('fleet');
