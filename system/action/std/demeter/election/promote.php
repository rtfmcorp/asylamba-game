<?php
#rplayer	id du joueur
#rcandidate id du candidat
#relection id election

$rPlayer = Utils::getHTTPData('rplayer');
$rElection = Utils::getHTTPData('relection');
$rCandidate = Utils::getHTTPData('rcandidate');

include_once DEMETER;
include_once ZEUS;

if ($rPlayer !== FALSE && $rElection !== FALSE && $rCandidate !== FALSE) {
	$_ELM = ASM::$elm->getCurrentSession();
	ASM::$elm->newSession();
	ASM::$elm->load(array('id' => $rElection));
	$_PAM = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => $rPlayer));
	$_CAM = ASM::$cam->getCurrentSession();
	ASM::$cam->newSession();
	ASM::$cam->load(array('rPlayer' => $rCandidate, 'rElection' => $rElection));

	if (ASM::$elm->size() > 0) {
		if (ASM::$pam->size() > 0) {
			if (ASM::$cam->size() > 0) {
				if (ASM::$elm->get()->rColor == ASM::$pam->get()->getRColor()) {
					$_VOM = ASM::$vom->getCurrentSession();
					ASM::$vom->newSession();
					ASM::$vom->load(array('rPlayer' => $rPlayer, 'rElection' => $rElection));

					if (ASM::$vom->get() == 0) {
						$_CLM = ASM::$clm->getCurrentSession();
						ASM::$clm->newSession();
						ASM::$clm->load(array('id' => ASM::$pam->get()->getRColor()));

						if(ASM::$clm->get()->electionStatement == Color::ELECTION) {
							$vote = new Vote();
							$vote->rPlayer = $rPlayer;
							$vote->rCandidate = $rCandidate;
							$vote->rElection = $rElection;
							ASM::$vom->add($vote);
							CTR::$alert->add('Vous avez voté.', ALERT_STD_SUCCESS);
						} else {
							CTR::$alert->add('Vous ne pouvez voter pour un candidat qu\'en période d\'élection.', ALERT_STD_ERROR);
						}

						ASM::$clm->changeSession($_CLM);
					} else {
						CTR::$alert->add('Vous avez déjà voté.', ALERT_STD_ERROR);
					}
				ASM::$vom->changeSession($_VOM);
				} else {
					CTR::$alert->add('Cette election ne se déroule pas dans la faction du joueur.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Ce candidat n\'existe pas.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Ce joueur n\'existe pas.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Cette election n\'existe pas.', ALERT_STD_ERROR);
	}

	ASM::$cam->changeSession($_CAM);
	ASM::$pam->changeSession($_PAM);
	ASM::$elm->changeSession($_ELM);
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}