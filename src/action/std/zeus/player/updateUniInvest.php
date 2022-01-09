<?php
# modify investments in university action

# int credit 		nouveau montant à investir

use App\Modules\Zeus\Resource\TutorialResource;
use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$tutorialHelper = $this->getContainer()->get(\Asylamba\Modules\Zeus\Helper\TutorialHelper::class);

$investment = $request->request->get('credit');

if ($investment !== FALSE) { 
	if ($investment <= 500000) {
		$playerManager->updateUniversityInvestment($session->get('playerId'), (int) $investment);

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
