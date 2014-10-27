<?php
#rplayer	id du joueur
#department

$rPlayer = Utils::getHTTPData('rplayer');
$department = Utils::getHTTPData('department');

include_once ZEUS;

if ($rPlayer !== FALSE && $department !== FALSE) {
	$_PAM2 = ASM::$pam->getCurrentsession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('status' => $department, 'rColor' => CTR::$data->get('playerInfo')->get('color')));
	if (ASM::$pam->size() == 0) {
		if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
			$_PAM = ASM::$pam->getCurrentsession();
			ASM::$pam->newSession();
			ASM::$pam->load(array('id' => $rPlayer));

			if (ASM::$pam->size() > 0) {
				if (ASM::$pam->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
					if (ASM::$pam->get()->status == PAM_PARLIAMENT) {
						if ($department > PAM_PARLIAMENT && $department < PAM_CHIEF) {
							ASM::$pam->get()->status = $department;
							
							$statusArray = ColorResource::getInfo(ASM::$pam->get()->rColor, 'status');
							$notif = new Notification();
							$notif->dSending = Utils::now();
							$notif->setRPlayer($rPlayer);
							$notif->setTitle('Votre êtes membre du gouvernement');
							$notif->addBeg()
								->addTxt('Vous avez été choisi pour être le ' . $statusArray[$department] . ' de votre faction.');
							ASM::$ntm->add($notif);

							CTR::$alert->add(ASM::$pam->get()->name . ' a rejoint votre gouvernement.', ALERT_STD_SUCCESS);	
						} else {
						CTR::$alert->add('Ce département est inconnu.', ALERT_STD_ERROR);
					}
					} else {
						CTR::$alert->add('Vous ne pouvez choisir qu\'un membre du sénat.', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('Vous ne pouvez pas choisir un joueur d\'une autre faction.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Ce joueur n\'existe pas.', ALERT_STD_ERROR);
			}

			ASM::$pam->changeSession($_PAM);
		} else {
			CTR::$alert->add('Vous n\'êtes pas le chef de votre faction.', ALERT_STD_ERROR);	
		}
	} else {
		CTR::$alert->add('Quelqu\'un occupe déjà ce poste.', ALERT_STD_ERROR);	
	}
	ASM::$pam->changeSession($_PAM2);
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}