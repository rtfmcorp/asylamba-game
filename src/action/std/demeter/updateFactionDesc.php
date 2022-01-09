<?php

use App\Classes\Exception\FormException;
use App\Modules\Zeus\Model\Player;

$colorManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\ColorManager::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');

if (($description = $request->request->get('description')) !== FALSE) {
	if (($player = $playerManager->get($session->get('playerId')))) {
		if ($player->status > Player::PARLIAMENT) {
			if ($description !== '' && strlen($description) < 25000) {
				$faction = $colorManager->get($player->rColor);
				$faction->description = $description;
				
				$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush($faction);
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
