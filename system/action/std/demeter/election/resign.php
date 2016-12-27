<?php
#rplayer	id du joueur

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');

if ($session->get('playerInfo')->get('status') > Player::PARLIAMENT && $session->get('playerInfo')->get('status') < Player::CHIEF) {
	$_PAM = $playerManager->getCurrentsession();
	$playerManager->newSession();
	$playerManager->load(array('id' => $session->get('playerId')));

	if ($playerManager->size() > 0) {
		$playerManager->get()->status = Player::PARLIAMENT;
		$session->get('playerInfo')->add('status', Player::PARLIAMENT);
		$response->flashbag->add('Vous n\'êtes plus membre du gouvernement.', Response::FLASHBAG_SUCCESS);
	} else {
		throw new ErrorException('Ce joueur n\'existe pas.');
	}

	$playerManager->changeSession($_PAM);
} else {
	throw new ErrorException('Vous n\'êtes pas dans le gouvernement de votre faction ou en êtes le chef.');
}