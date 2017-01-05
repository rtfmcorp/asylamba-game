<?php

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Modules\Ares\Model\Commander;
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

$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commanderId, 'c.rPlayer' => $session->get('playerId')));

if ($commanderManager->size() !== 1) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas');
}
$commander = $commanderManager->get();

$S_OBM = $orbitalBaseManager->getCurrentSession();
$orbitalBaseManager->newSession();
$orbitalBaseManager->load(array('rPlace' => $commander->rBase));

# checker si on a assez de place !!!!!
if ($commander->line == 1) {
	$S_COM2 = $commanderManager->getCurrentSession();
	$commanderManager->newSession();
	$commanderManager->load(array('c.rBase' => $commander->rBase, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 2));
	$nbrLine2 = $commanderManager->size();

	if ($nbrLine2 < PlaceResource::get($orbitalBaseManager->get()->typeOfBase, 'r-line')) {
		$commander->line = 2;

		$response->redirect();

	} else {
		$commander->line = 2;
		$commanderManager->get()->line = 1;
		$response->redirect();
		$session->addFlashbag('Votre commandant ' . $commander->getName() . ' a échangé sa place avec ' . $commanderManager->get()->name . '.', Flashbag::TYPE_SUCCESS);
	}
	$commanderManager->changeSession($S_COM2);
} else {
	$S_COM2 = $commanderManager->getCurrentSession();
	$commanderManager->newSession();
	$commanderManager->load(array('c.rBase' => $commander->rBase, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 1));
	$nbrLine1 = $commanderManager->size();

	# tutorial
	if ($session->get('playerInfo')->get('stepDone') !== true && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::MOVE_FLEET_LINE) {
		$tutorialHelper->setStepDone();
	}

	if ($nbrLine1 < PlaceResource::get($orbitalBaseManager->get()->typeOfBase, 'l-line')) {
		$commander->line = 1;

		$response->redirect();
	} else {
		$commander->line = 1;
		$commanderManager->get()->line = 2;
		$response->redirect();
		$session->addFlashbag('Votre commandant ' . $commander->getName() . ' a échangé sa place avec ' . $commanderManager->get()->name . '.', Flashbag::TYPE_SUCCESS);
	}
	$commanderManager->changeSession($S_COM2);
}

$commanderManager->changeSession($S_COM2);
$orbitalBaseManager->changeSession($S_OBM);

$commanderManager->changeSession($S_COM1);