<?php

use Asylamba\Classes\Exception\FormException;
use Asylamba\Modules\Zeus\Model\Player;

$colorManager = $this->getContainer()->get('demeter.color_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');

if (($description = $request->request->get('description')) !== FALSE) {
	if (($player = $playerManager->get($session->get('playerId')))) {
		if ($player->status > Player::PARLIAMENT) {
			if ($description !== '' && strlen($description) < 25000) {
				$faction = $colorManager->get($player->rColor);
				$faction->description = $description;
				
				$this->getContainer()->get('entity_manager')->flush($faction);
			} else {
				throw new FormException('La description est vide ou trop longue');
			}
		} else {
			throw new FormException('Vous n\'avez pas les droits pour poster une description');
		}
	} else {
		throw new FormException('Vous n\'existez pas');
	}
} else {
	throw new FormException('Pas assez d\'informations pour écrire une description');
}