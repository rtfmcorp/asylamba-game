<?php
# modify investments in university action

# int credit 		nouveau montant à investir

use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

$credit = $request->request->get('credit');


if ($credit !== FALSE) { 
	if ($credit <= 500000) {
		$S_PAM1 = $playerManager->getCurrentSession();
		$playerManager->newSession();
		$playerManager->load(array('id' => $session->get('playerId')));
		$playerManager->get()->iUniversity = $credit;

		# tutorial
		if ($session->get('playerInfo')->get('stepDone') == FALSE &&
			$session->get('playerInfo')->get('stepTutorial') === TutorialResource::MODIFY_UNI_INVEST) {
			$tutorialHelper->setStepDone();
		}

		$response->flashbag->add('L\'investissement dans l\'université a été modifié', Response::FLASHBAG_SUCCESS);

		$playerManager->changeSession($S_PAM1);
	} else {
		throw new ErrorException('La limite maximale d\'investissement dans l\'Université est de 500\'000 crédits.');
	}
} else {
	throw new FormException('pas assez d\'informations pour modifier cet investissement');
}