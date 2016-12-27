<?php
#rplayer	id du joueur

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

$rPlayer = $request->query->get('rplayer');

if ($rPlayer !== FALSE) {
	if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
		$_PAM = $playerManager->getCurrentsession();
		$playerManager->newSession();
		$playerManager->load(array('id' => $rPlayer));

		if ($playerManager->size() > 0) {
			if ($playerManager->get()->rColor == $session->get('playerInfo')->get('color')) {
				if ($playerManager->get()->status > Player::PARLIAMENT) {
					$statusArray = ColorResource::getInfo($playerManager->get()->rColor, 'status');
					$notif = new Notification();
					$notif->setRPlayer($rPlayer);
					$notif->setTitle('Eviction du gouvernement');
					$notif->addBeg()
						->addTxt('Vous avez été renvoyé du poste de ' . $statusArray[$playerManager->get()->status - 1] . ' de votre faction.');
					$notificationManager->add($notif);

					$playerManager->get()->status = Player::PARLIAMENT;

				} else {
					throw new ErrorException('Vous ne pouvez choisir qu\'un membre du gouvernement.');
				}
			} else {
				throw new ErrorException('Vous ne pouvez pas virer un joueur d\'une autre faction.');
			}
		} else {
			throw new ErrorException('Ce joueur n\'existe pas.');
		}
		$playerManager->changeSession($_PAM);
	} else {
		throw new ErrorException('Vous n\'êtes pas le chef de votre faction.');	
	}
} else {
	throw new ErrorException('Informations manquantes.');
}