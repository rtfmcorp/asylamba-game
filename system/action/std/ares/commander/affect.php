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
$session  = $this->getContainer()->get('session_wrapper');
$response = $this->getContainer()->get('app.response');

if (($commander = $commanderManager->get($commanderId)) === null) {
    throw new ErrorException('Cet officier n\'existe pas ou ne vous appartient pas');
}

$orbitalBase = $orbitalBaseManager->get($commander->rBase);

# checker si on a assez de place !!!!!
$nbrLine1 = $commanderManager->countCommandersByLine($commander->rBase, 1);
$nbrLine2 = $commanderManager->countCommandersByLine($commander->rBase, 2);

if ($commander->statement == Commander::INSCHOOL || $commander->statement == Commander::RESERVE) {
    if ($nbrLine2 < PlaceResource::get($orbitalBase->typeOfBase, 'r-line')) {
        $commander->dAffectation = Utils::now();
        $commander->statement = Commander::AFFECTED;
        $commander->line = 2;

        # tutorial
        if ($session->get('playerInfo')->get('stepDone') == false && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::AFFECT_COMMANDER) {
            $tutorialHelper->setStepDone();
        }

        $session->addFlashbag('Votre officier ' . $commander->getName() . ' a bien été affecté en force de réserve', Flashbag::TYPE_SUCCESS);
        $response->redirect('fleet/commander-' . $commander->id . '/sftr-2');
    } elseif ($nbrLine1 < PlaceResource::get($orbitalBase->typeOfBase, 'l-line')) {
        $commander->dAffectation =Utils::now();
        $commander->statement = Commander::AFFECTED;
        $commander->line = 1;

        # tutorial
        if ($session->get('playerInfo')->get('stepDone') == false && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::AFFECT_COMMANDER) {
            $tutorialHelper->setStepDone();
        }

        $session->addFlashbag('Votre officier ' . $commander->getName() . ' a bien été affecté en force active', Flashbag::TYPE_SUCCESS);
        $response->redirect('fleet/commander-' . $commander->id . '/sftr-2');
    } else {
        throw new ErrorException('Votre base a dépassé la capacité limite de officiers en activité');
    }
} elseif ($commander->statement == Commander::AFFECTED) {
    $baseCommanders = $commanderManager->getBaseCommanders($commander->rBase, [Commander::INSCHOOL]);

    $commander->uCommander = Utils::now();
    if (count($baseCommanders) < PlaceResource::get($orbitalBase->typeOfBase, 'school-size')) {
        $commander->statement = Commander::INSCHOOL;
        $session->addFlashbag('Votre officier ' . $commander->getName() . ' a été remis à l\'école', Flashbag::TYPE_SUCCESS);
        $commanderManager->emptySquadrons($commander);
    } else {
        $commander->statement = Commander::RESERVE;
        $session->addFlashbag('Votre officier ' . $commander->getName() . ' a été remis dans la réserve de l\'armée', Flashbag::TYPE_SUCCESS);
        $commanderManager->emptySquadrons($commander);
    }
    $response->redirect('bases/view-school');
} else {
    throw new ErrorException('Le status de votre officier ne peut pas être modifié');
}
$this->getContainer()->get('entity_manager')->flush();
