<?php
#rplayer	id du joueur

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Hermes\Model\Notification;

$rPlayer = Utils::getHTTPData('rplayer');

if ($rPlayer !== FALSE) {
	if (CTR::$data->get('playerInfo')->get('status') == Player::CHIEF) {
		$_PAM = ASM::$pam->getCurrentsession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => $rPlayer));

		if (ASM::$pam->size() > 0) {
			if (ASM::$pam->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
				if (ASM::$pam->get()->status > Player::PARLIAMENT) {
					$statusArray = ColorResource::getInfo(ASM::$pam->get()->rColor, 'status');
					$notif = new Notification();
					$notif->setRPlayer($rPlayer);
					$notif->setTitle('Eviction du gouvernement');
					$notif->addBeg()
						->addTxt('Vous avez été renvoyé du poste de ' . $statusArray[ASM::$pam->get()->status - 1] . ' de votre faction.');
					ASM::$ntm->add($notif);

					ASM::$pam->get()->status = Player::PARLIAMENT;

				} else {
					CTR::$alert->add('Vous ne pouvez choisir qu\'un membre du gouvernement.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Vous ne pouvez pas virer un joueur d\'une autre faction.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Ce joueur n\'existe pas.', ALERT_STD_ERROR);
		}

		ASM::$pam->changeSession($_PAM);
	} else {
		CTR::$alert->add('Vous n\'êtes pas le chef de votre faction.', ALERT_STD_ERROR);	
	}
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}