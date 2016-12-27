<?php

use Asylamba\Classes\Exception\FormException;

$colorManager = $this->getContainer()->get('demeter.color_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
# string description 	description à envoyer

$description = $request->request->get('description');

if ($description !== FALSE) {
	$S_PAM1 = $playerManager->getCurrentSession();
	$playerManager->newSession(FALSE);
	$playerManager->load(array('id' => $session->get('playerId')));

	if ($playerManager->size() == 1) {
		if ($playerManager->get()->status > Player::PARLIAMENT) {
			if ($description !== '' && strlen($description) < 25000) {
				$S_COL_1 = $colorManager->getCurrentSession();
				$colorManager->newSession();
				$colorManager->load(array('id' => $playerManager->get()->rColor));

				$colorManager->get()->description = $description;				
				
				$colorManager->changeSession($S_COL_1);
			} else {
				throw new FormException('La description est vide ou trop longue');
			}
		} else {
			throw new FormException('Vous n\'avez pas les droits pour poster une description');
		}
	} else {
		throw new FormException('Vous n\'existez pas');
	}

	$playerManager->changeSession($S_PAM1);
} else {
	throw new FormException('Pas assez d\'informations pour écrire une description');
}