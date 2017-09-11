<?php
# modify investments in university action

# int credit 		nouveau montant à investir

use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

$investment = $request->request->get('credit');

if ($investment !== false) {
    if ($investment <= 500000) {
        $playerManager->updateUniversityInvestment($session->get('playerId'), (int) $investment);

        # tutorial
        if ($session->get('playerInfo')->get('stepDone') == false &&
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
