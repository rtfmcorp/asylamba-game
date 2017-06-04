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
		if (($minister = $playerManager->get($rPlayer)) !== null) {
			if ($minister->rColor == $session->get('playerInfo')->get('color')) {
				if ($minister->status > Player::PARLIAMENT) {
					$statusArray = ColorResource::getInfo($minister->rColor, 'status');
					$notif = new Notification();
					$notif->setRPlayer($rPlayer);
					$notif->setTitle('Eviction du gouvernement');
					$notif->addBeg()
						->addTxt('Vous avez été renvoyé du poste de ' . $statusArray[$minister->status - 1] . ' de votre faction.');
					$notificationManager->add($notif);

					$minister->status = Player::PARLIAMENT;
					
					$this->getContainer()->get('entity_manager')->flush($minister);
				} else {
					throw new ErrorException('Vous ne pouvez choisir qu\'un membre du gouvernement.');
				}
			} else {
				throw new ErrorException('Vous ne pouvez pas virer un joueur d\'une autre faction.');
			}
		} else {
			throw new ErrorException('Ce joueur n\'existe pas.');
		}
	} else {
		throw new ErrorException('Vous n\'êtes pas le chef de votre faction.');	
	}
} else {
	throw new ErrorException('Informations manquantes.');
}