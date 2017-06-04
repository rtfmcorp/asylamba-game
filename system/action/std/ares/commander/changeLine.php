<?php

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Classes\Exception\ErrorException;

# change of line a commander

# int id 	 		id du commandant

if(($commanderId = $this->getContainer()->get('app.request')->query->get('id')) === null) {
	throw new ErrorException('erreur dans le traitement de la requête');	
}

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');
$session = $this->getContainer()->get('app.session');
$response = $this->getContainer()->get('app.response');

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $session->get('playerId')) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas');
}
$orbitalBase = $orbitalBaseManager->get($commander->rBase);

# checker si on a assez de place !!!!!
if ($commander->line == 1) {
	$secondLineCommanders = $commanderManager->getCommandersByLine($commander->rBase, 2);

	if (count($secondLineCommanders) < PlaceResource::get($orbitalBase->typeOfBase, 'r-line')) {
		$commander->line = 2;

		$response->redirect($session->getLastHistory());

	} else {
		$commander->line = 2;
		$secondLineCommanders[0]->line = 1;
		$response->redirect($session->getLastHistory());
		$session->addFlashbag('Votre commandant ' . $commander->getName() . ' a échangé sa place avec ' . $commander->name . '.', Flashbag::TYPE_SUCCESS);
	}
} else {
	$firstLineCommanders = $commanderManager->getCommandersByLine($commander->rBase, 1);

	# tutorial
	if ($session->get('playerInfo')->get('stepDone') !== true && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::MOVE_FLEET_LINE) {
		$tutorialHelper->setStepDone();
	}

	if (count($firstLineCommanders) < PlaceResource::get($orbitalBase->typeOfBase, 'l-line')) {
		$commander->line = 1;

		$response->redirect($session->getLastHistory());
	} else {
		$commander->line = 1;
		$firstLineCommanders[0]->line = 2;
		$response->redirect($session->getLastHistory());
		$session->addFlashbag('Votre commandant ' . $commander->getName() . ' a échangé sa place avec ' . $commander->name . '.', Flashbag::TYPE_SUCCESS);
	}
}
$this->getContainer()->get('entity_manager')->flush();