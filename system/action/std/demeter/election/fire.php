<?php
#rplayer	id du joueur

$rPlayer = Utils::getHTTPData('rplayer');

include_once ZEUS;
include_once DEMETER;

if ($rPlayer !== FALSE) {
	if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
		$_PAM = ASM::$pam->getCurrentsession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => $rPlayer));

		if (ASM::$pam->size() > 0) {
			if (ASM::$pam->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
				if (ASM::$pam->get()->status > PAM_PARLIAMENT) {
					$statusArray = ColorResource::getInfo(ASM::$pam->get()->rColor, 'status');
					$notif = new Notification();
					$notif->setRPlayer($rPlayer);
					$notif->setTitle('Eviction du gouvernement');
					$notif->addBeg()
						->addTxt('Vous avez été renvoyé du poste de ' . $statusArray[ASM::$pam->get()->status - 1] . ' de votre faction.');
					ASM::$ntm->add($notif);

					ASM::$pam->get()->status = PAM_PARLIAMENT;

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