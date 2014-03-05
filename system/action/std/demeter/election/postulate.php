<?php
#rplayer	id du joueur
#relection id election
#program

$rPlayer = Utils::getHTTPData('rplayer');
$rElection = Utils::getHTTPData('relection');
$program = Utils::getHTTPData('program');

include_once DEMETER;
include_once ZEUS;

if ($rPlayer !== FALSE && $rElection !== FALSE) {
	$_ELM = ASM::$elm->getCurrentSession();
	ASM::$elm->newSession();
	ASM::$elm->load(array('id' => $rElection));
	$_PAM = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => $rPlayer));

	if (ASM::$elm->size() > 0) {
		if (ASM::$pam->size() > 0) {
			if (ASM::$elm->get()->rColor == ASM::$pam->get()->getRColor()) {
				if (ASM::$pam->get()->getStatus() > PAM_STANDARD) {
					$_CLM = ASM::$clm->getCurrentSession();
					ASM::$clm->newSession();
					ASM::$clm->load(array('id' => ASM::$pam->get()->getRColor()));
					$_CAM = ASM::$cam->getCurrentSession();
					ASM::$cam->newSession();
					ASM::$cam->load(array('rPlayer' => ASM::$pam->get()->getId(), 'rElection' => $rElection));

					if(ASM::$clm->get()->electionStatement == Color::CAMPAIGN) {
						if (ASM::$cam->size() == 0) {
							$candidate = new candidate();
							$candidate->rElection = $rElection;
							$candidate->rPlayer = $rPlayer; 
							$candidate->rElection = ($program != FALSE) ? $rElection: ''; 
							ASM::$cam->add($candidate);

							CTR::$alert->add('Candidature déposée.', ALERT_STD_SUCCESS);
						} else {
							ASM::$cam->deleteById(ASM::$cam->get()->getId());
							CTR::$alert->add('Candidature retirée.', ALERT_STD_SUCCESS);
						}
					} else {
						CTR::$alert->add('Vous ne pouvez présenter ou retirer votre candidature qu\'en période de campagne.', ALERT_STD_ERROR);
					}

					ASM::$clm->changeSession($_CAM);
					ASM::$clm->changeSession($_CLM);
				} else {
					CTR::$alert->add('Vous ne pouvez pas vous présenter, vous ne faite pas partie de l\'élite.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Cette election ne se déroule pas dans la faction du joueur.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Ce joueur n\'existe pas.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Cette election n\'existe pas.', ALERT_STD_ERROR);
	}

	ASM::$pam->changeSession($_PAM);
	ASM::$elm->changeSession($_ELM);
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}