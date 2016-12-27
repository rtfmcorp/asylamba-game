<?php

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

# search player profile
$playerManager = $this->getContainer()->get('zeus.player_manager');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');

# string name 	nom du joueur
$playerid = $request->query->get('playerid');

if ($playerid !== FALSE) {
	$S_PAM1 = $playerManager->getCurrentSession();
	$playerManager->newSession();
	$playerManager->load(array('id' => $playerid));
	
	if ($playerManager->size() == 1) {
		$response->redirect('embassy/player-' . $playerManager->get()->getId());
	} else {
		throw new ErrorException('Aucun joueur ne correspond à votre recherche.');
	}

	$playerManager->changeSession($S_PAM1);
} else {
	throw new FormException('pas assez d\'informations pour chercher un joueur');
}