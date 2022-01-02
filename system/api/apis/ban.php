<?php

use Asylamba\Modules\Zeus\Model\Player;

$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);

if ($request->query->exist('bindkey')) {
	if (($player = $playerManager->getByBindKey($request->query->get('bindkey')))) {
		$player->setStatement(Player::BANNED);
		$this->getContainer()->get('entity_manager')->flush($player);
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
