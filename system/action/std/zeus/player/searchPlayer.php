<?php

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

# search player profile
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');

# string name 	nom du joueur
$playerid = $request->request->get('playerid');

if ($playerid !== FALSE) {
	if (($player = $playerManager->get($playerid)) !== null) {
		$response->redirect('embassy/player-' . $player->getId());
	} else {
		throw new ErrorException('Aucun joueur ne correspond à votre recherche.');
	}
} else {
	throw new FormException('pas assez d\'informations pour chercher un joueur');
}
