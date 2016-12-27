<?php
#rplayer	id du joueur
#department

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

$rPlayer = $request->query->get('rplayer');
$department = $request->query->get('department');

if ($rPlayer !== FALSE && $department !== FALSE) {
	$_PAM2 = $playerManager->getCurrentsession();
	$playerManager->newSession();
	$playerManager->load(array('status' => $department, 'rColor' => $session->get('playerInfo')->get('color')));
	if ($playerManager->size() == 0) {
		if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
			$_PAM = $playerManager->getCurrentsession();
			$playerManager->newSession();
			$playerManager->load(array('id' => $rPlayer));

			if ($playerManager->size() > 0) {
				if ($playerManager->get()->rColor == $session->get('playerInfo')->get('color')) {
					if ($playerManager->get()->status == Player::PARLIAMENT) {
						if ($department > Player::PARLIAMENT && $department < Player::CHIEF) {
							$playerManager->get()->status = $department;
							
							$statusArray = ColorResource::getInfo($playerManager->get()->rColor, 'status');
							$notif = new Notification();
							$notif->setRPlayer($rPlayer);
							$notif->setTitle('Nomination au gouvernement');
							$notif->addBeg()
								->addTxt('Vous avez été choisi pour être le ' . $statusArray[$department - 1] . ' de votre faction.');
							$notificationManager->add($notif);

							$response->flashbag->add($playerManager->get()->name . ' a rejoint votre gouvernement.');	
						} else {
						throw new ErrorException('Ce département est inconnu.');
					}
					} else {
						throw new ErrorException('Vous ne pouvez choisir qu\'un membre du sénat.');
					}
				} else {
					throw new ErrorException('Vous ne pouvez pas choisir un joueur d\'une autre faction.');
				}
			} else {
				throw new ErrorException('Ce joueur n\'existe pas.');
			}

			$playerManager->changeSession($_PAM);
		} else {
			throw new ErrorException('Vous n\'êtes pas le chef de votre faction.');	
		}
	} else {
		throw new ErrorException('Quelqu\'un occupe déjà ce poste.');	
	}
	$playerManager->changeSession($_PAM2);
} else {
	throw new ErrorException('Informations manquantes.');
}