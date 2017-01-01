<?php

use Asylamba\Modules\Zeus\Model\Player;

$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');


if ($request->query->exist('bindkey')) {
	$S_PAM_1 = $playerManager->getCurrentSession();
	$playerManager->newSession();
	$playerManager->load(array('bind' => $request->query->get('bindkey')));

	if ($playerManager->size() == 1) {
		$playerManager->get()->setStatement(Player::BANNED);

		echo serialize(array('statement' => 'success'));
	} else {
		echo serialize(array(
			'statement' => 'error',
			'message' => 'Joueur inconnu'
		));
	}

	$playerManager->changeSession($S_PAM_1);
} else {
	echo serialize(array(
		'statement' => 'error',
		'message' => 'Donnée manquante'
	));
}