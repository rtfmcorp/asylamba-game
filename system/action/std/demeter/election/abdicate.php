<?php
#rplayer	id du joueur
#department

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

$rPlayer = $request->query->get('rplayer');

if ($statusArray = ColorResource::getInfo($session->get('playerInfo')->get('color'), 'regime') == Color::DEMOCRATIC) {
	if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
		$_CLM = $colorManager->getCurrentsession();
		$colorManager->newSession();
		$colorManager->load(['id' => $session->get('playerInfo')->get('color')]);

		if ($colorManager->get()->electionStatement == Color::MANDATE) {
			$date = new \DateTime(Utils::now());
			$date->modify('-' . $colorManager->get()->mandateDuration . ' second');
			$date = $date->format('Y-m-d H:i:s');
			$colorManager->get()->dLastElection = $date;			
			$session->addFlashbag('Des élections anticipées vont être lancées.', Flashbag::TYPE_SUCCESS);	
		} else {
			throw new ErrorException('Des élections sont déjà en cours.');	
		}
		$colorManager->changeSession($_CLM);
	} else {
		throw new ErrorException('Vous n\'êtes pas le chef de votre faction.');
	}
} else {
	if ($rPlayer !== FALSE) {
		$_PAM2 = $playerManager->getCurrentsession();
		$playerManager->newSession();
		if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
			if (($heir = $playerManager->get($rPlayer)) !== null) {
				if ($heir->rColor == $session->get('playerInfo')->get('color')) {
					if ($heir->status >= Player::PARLIAMENT) {
						$_CLM = $colorManager->getCurrentsession();
						$colorManager->newSession();
						$colorManager->load(['id' => $session->get('playerInfo')->get('color')]);

						if ($colorManager->get()->electionStatement == Color::MANDATE) {
							$heir->status = Player::CHIEF;
							// The player is now a member of Parliament
							$playerManager->get($session->get('playerId'))->status = Player::PARLIAMENT;
							$session->get('playerInfo')->add('status', Player::PARLIAMENT);

							$statusArray = ColorResource::getInfo($heir->rColor, 'status');
							$notif = new Notification();
							$notif->setRPlayer($rPlayer);
							$notif->setTitle('Héritier du Trône.');
							$notif->addBeg()
								->addTxt('Vous avez été choisi par le ' . $statusArray[5] . ' de votre faction pour être son successeur, vous prenez la tête du gouvernement immédiatement.');
							$notificationManager->add($notif);

							$this->getContainer()->get('entity_manager')->flush();
							$session->addFlashbag($playerManager->get()->name . ' est désigné comme votre successeur.', Flashbag::TYPE_SUCCESS);	
						} else {
							throw new ErrorException('vous ne pouvez pas abdiquer pendant un putsch.');	
						}
						$colorManager->changeSession($_CLM);
						
					} else {
						throw new ErrorException('Vous ne pouvez choisir qu\'un membre du sénat ou du gouvernement.');
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
		$playerManager->changeSession($_PAM2);
	} else {
		throw new ErrorException('Informations manquantes.');
	}
}