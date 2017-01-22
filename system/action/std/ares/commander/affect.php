<?php

# affect a commander

# int id 	 		id du officier

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;

if (($commanderId = $this->getContainer()->get('app.request')->query->get('id')) === null) {
	throw new ErrorException('erreur dans le traitement de la requête');
}
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');
$session  = $this->getContainer()->get('app.session');
$response = $this->getContainer()->get('app.response');

$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commanderId, 'c.rPlayer' => $session->get('playerId')));

if ($commanderManager->size() !== 1) {
	throw new ErrorException('Ce officier n\'existe pas ou ne vous appartient pas');
}
$commander = $commanderManager->get();

$orbitalBase = $orbitalBaseManager->get($commander->rBase);

# checker si on a assez de place !!!!!
$S_COM2 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.rBase' => $commander->rBase, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 2));
$nbrLine2 = $commanderManager->size();

$commanderManager->newSession();
$commanderManager->load(array('c.rBase' => $commander->rBase, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 1));
$nbrLine1 = $commanderManager->size();

if ($commander->statement == Commander::INSCHOOL || $commander->statement == Commander::RESERVE) {
	if ($nbrLine2 < PlaceResource::get($orbitalBase->typeOfBase, 'r-line')) {
		$commander->dAffectation = Utils::now();
		$commander->statement = Commander::AFFECTED;
		$commander->line = 2;

		# tutorial
		if ($session->get('playerInfo')->get('stepDone') == FALSE && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::AFFECT_COMMANDER) {
			$tutorialHelper->setStepDone();
		}

		$session->addFlashbag('Votre officier ' . $commander->getName() . ' a bien été affecté en force de réserve', Flashbag::TYPE_SUCCESS);
		$response->redirect('fleet/commander-' . $commander->id . '/sftr-2');
	} elseif ($nbrLine1 < PlaceResource::get($orbitalBase->typeOfBase, 'l-line')) {
		$commander->dAffectation =Utils::now();
		$commander->statement = Commander::AFFECTED;
		$commander->line = 1;

		# tutorial
		if ($session->get('playerInfo')->get('stepDone') == FALSE && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::AFFECT_COMMANDER) {
			$tutorialHelper->setStepDone();
		}

		$session->addFlashbag('Votre officier ' . $commander->getName() . ' a bien été affecté en force active', Flashbag::TYPE_SUCCESS);
		$response->redirect('fleet/commander-' . $commander->id . '/sftr-2');
	} else {
		throw new ErrorException('Votre base a dépassé la capacité limite de officiers en activité');			
	}
} elseif ($commander->statement == Commander::AFFECTED) {
	$S_COM3 = $commanderManager->getCurrentSession();
	$commanderManager->newSession();
	$commanderManager->load(array('c.rBase' => $commander->rBase, 'c.statement' => Commander::INSCHOOL));

	$commander->uCommander = Utils::now();
	if ($commanderManager->size() < PlaceResource::get($orbitalBase->typeOfBase, 'school-size')) {
		$commander->statement = Commander::INSCHOOL;
		$session->addFlashbag('Votre officier ' . $commander->getName() . ' a été remis à l\'école', Flashbag::TYPE_SUCCESS);
		$commanderManager->emptySquadrons($commander);
	} else {
		$commander->statement = Commander::RESERVE;
		$session->addFlashbag('Votre officier ' . $commander->getName() . ' a été remis dans la réserve de l\'armée', Flashbag::TYPE_SUCCESS);
		$commanderManager->emptySquadrons($commander);
	}
	$commanderManager->changeSession($S_COM3);
	$response->redirect('bases/view-school');
} else {
	throw new ErrorException('Le status de votre officier ne peut pas être modifié');
}

$commanderManager->changeSession($S_COM2);
$commanderManager->changeSession($S_COM1);