<?php

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Zeus\Model\Player;

$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('app.session');
$response = $this->getContainer()->get('app.response');

if (($player = $playerManager->get($session->get('playerId'))) !== null) {
	# sending API call to delete account link to server
	$success = $this->getContainer()->get('api')->abandonServer($player->bind, APP_ID);

	if ($success) {
		$player->bind = $player->bind . 'ABANDON';
		$player->statement = Player::DELETED;
		
		$this->getContainer()->get('entity_manager')->flush($player);
		# clean session
		$session->destroy();
		$response->redirect(GETOUT_ROOT . 'serveurs', TRUE);
	} else {
		throw new ErrorException('Une erreur s\'est produite sur le portail. Contactez un administrateur pour résoudre ce problème.');
	}
} else {
	throw new ErrorException('Une erreur s\'est produite. Contactez un administrateur pour résoudre ce problème.');
}
