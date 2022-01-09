<?php

use App\Classes\Library\Flashbag;
use App\Modules\Gaia\Resource\PlaceResource;
use App\Modules\Zeus\Resource\TutorialResource;
use App\Classes\Exception\ErrorException;

# change of line a commander

# int id 	 		id du commandant

if(($commanderId = $this->getContainer()->get('app.request')->query->get('id')) === null) {
	throw new ErrorException('erreur dans le traitement de la requête');	
}

$commanderManager = $this->getContainer()->get(\App\Modules\Ares\Manager\CommanderManager::class);
$orbitalBaseManager = $this->getContainer()->get(\App\Modules\Athena\Manager\OrbitalBaseManager::class);
$tutorialHelper = $this->getContainer()->get(\App\Modules\Zeus\Helper\TutorialHelper::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
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
$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush();
