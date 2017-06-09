<?php
# modify investments in university action

# int credit 		nouveau montant à investir

use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

$credit = $request->request->get('credit');

if ($credit !== FALSE) { 
	if ($credit <= 500000) {
		$player = $playerManager->get($session->get('playerId'));
		$player->iUniversity = (int) $credit;
		$this->getContainer()->get('entity_manager')->flush($player);

		# tutorial
		if ($session->get('playerInfo')->get('stepDone') == FALSE &&
			$session->get('playerInfo')->get('stepTutorial') === TutorialResource::MODIFY_UNI_INVEST) {
			$tutorialHelper->setStepDone();
		}

		$session->addFlashbag('L\'investissement dans l\'université a été modifié', Flashbag::TYPE_SUCCESS);
	} else {
		throw new ErrorException('La limite maximale d\'investissement dans l\'Université est de 500\'000 crédits.');
	}
} else {
	throw new FormException('pas assez d\'informations pour modifier cet investissement');
}