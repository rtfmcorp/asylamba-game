<?php

use Asylamba\Classes\Worker\API;
use Asylamba\Classes\Exception\ErrorException;

$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('app.session');
$response = $this->getContainer()->get('app.response');

$S_PAM1 = $playerManager->getCurrentSession();
$playerManager->newSession();
$playerManager->load(array('id' => $session->get('playerId')));

if ($playerManager->size() == 1) {

	# sending API call to delete account link to server
	$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
	$success = $api->abandonServer($playerManager->get()->bind, APP_ID);

	if ($success) {

		$playerManager->get()->bind = $playerManager->get()->bind . 'ABANDON';
		$playerManager->get()->statement = Player::DELETED;
		
		# clean session
		$session->destroy();
		$response->redirect(GETOUT_ROOT . 'serveurs', TRUE);

	} else {
		throw new ErrorException('Une erreur s\'est produite sur le portail. Contactez un administrateur pour résoudre ce problème.');
	}

} else {
	throw new ErrorException('Une erreur s\'est produite. Contactez un administrateur pour résoudre ce problème.');
}

$playerManager->changeSession($S_PAM1);