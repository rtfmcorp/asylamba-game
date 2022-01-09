<?php

use App\Modules\Zeus\Model\Player;

$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);

if ($request->query->exist('bindkey')) {
	if (($player = $playerManager->getByBindKey($request->query->get('bindkey')))) {
		$player->setStatement(Player::BANNED);
		$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush($player);
		echo serialize(array('statement' => 'success'));
	} else {
		echo serialize(array(
			'statement' => 'error',
			'message' => 'Joueur inconnu'
		));
	}
} else {
	echo serialize(array(
		'statement' => 'error',
		'message' => 'Donnée manquante'
	));
}
