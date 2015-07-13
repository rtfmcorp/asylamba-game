<?php
#rplayer	id du joueur
#department

$rPlayer = Utils::getHTTPData('rplayer');

include_once ZEUS;
include_once DEMETER;

if ($statusArray = ColorResource::getInfo(CTR::$data->get('playerInfo')->get('color'), 'regime') == Color::DEMOCRATIC) {
	if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
		$_CLM = ASM::$clm->getCurrentsession();
		ASM::$clm->newSession();
		ASM::$clm->load(['id' => CTR::$data->get('playerInfo')->get('color')]);

		if (ASM::$clm->get()->electionStatement == Color::MANDATE) {
			$date = new DateTime(Utils::now());
			$date->modify('-' . ASM::$clm->get()->mandateDuration . ' second');
			$date = $date->format('Y-m-d H:i:s');
			ASM::$clm->get()->dLastElection = $date;			
			CTR::$alert->add('Des élections anticipées vont être lancées.', ALERT_STD_SUCCESS);	
		} else {
			CTR::$alert->add('Des élections sont déjà en cours.', ALERT_STD_ERROR);	
		}
		ASM::$clm->changeSession($_CLM);
	} else {
		CTR::$alert->add('Vous n\'êtes pas le chef de votre faction.', ALERT_STD_ERROR);
	}
} else {
	if ($rPlayer !== FALSE) {
		$_PAM2 = ASM::$pam->getCurrentsession();
		ASM::$pam->newSession();
		if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
			$_PAM = ASM::$pam->getCurrentsession();
			ASM::$pam->newSession();
			ASM::$pam->load(array('id' => $rPlayer));

			if (ASM::$pam->size() > 0) {
				if (ASM::$pam->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
					if (ASM::$pam->get()->status >= PAM_PARLIAMENT) {
						$_CLM = ASM::$clm->getCurrentsession();
						ASM::$clm->newSession();
						ASM::$clm->load(['id' => CTR::$data->get('playerInfo')->get('color')]);

						if (ASM::$clm->get()->electionStatement == Color::MANDATE) {
							ASM::$pam->get()->status = PAM_CHIEF;
							
							$_PAM23 = ASM::$pam->getCurrentsession();
							ASM::$pam->newSession();
							ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
							ASM::$pam->get()->status = PAM_PARLIAMENT;
							CTR::$data->get('playerInfo')->add('status', PAM_PARLIAMENT);
							ASM::$pam->changeSession($_PAM23);

							$statusArray = ColorResource::getInfo(ASM::$pam->get()->rColor, 'status');
							$notif = new Notification();
							$notif->setRPlayer($rPlayer);
							$notif->setTitle('Héritier du Trône.');
							$notif->addBeg()
								->addTxt('Vous avez été choisi par le ' . $statusArray[5] . ' de votre faction pour être son successeur, vous prenez la tête du gouvernement immédiatement.');
							ASM::$ntm->add($notif);

							CTR::$alert->add(ASM::$pam->get()->name . ' est désigné comme votre successeur.', ALERT_STD_SUCCESS);	
						} else {
							CTR::$alert->add('vous ne pouvez pas abdiquer pendant un putsch.', ALERT_STD_ERROR);	
						}
						ASM::$clm->changeSession($_CLM);
						
					} else {
						CTR::$alert->add('Vous ne pouvez choisir qu\'un membre du sénat ou du gouvernement.', ALERT_STD_ERROR);
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
		ASM::$pam->changeSession($_PAM2);
	} else {
		CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
	}
}