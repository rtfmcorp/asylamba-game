<?php
#rplayer	id du joueur
#rcandidate id du candidat
#relection id election

$rElection = Utils::getHTTPData('relection');
$rCandidate = Utils::getHTTPData('rcandidate');

include_once DEMETER;
include_once ZEUS;

if ($rElection !== FALSE && $rCandidate !== FALSE) {
	$_ELM = ASM::$elm->getCurrentSession();
	ASM::$elm->newSession();
	ASM::$elm->load(array('id' => $rElection));
	$_CAM = ASM::$cam->getCurrentSession();
	ASM::$cam->newSession();
	ASM::$cam->load(array('rPlayer' => $rCandidate, 'rElection' => $rElection));

	$_PAM = ASM::$pam->getCurrentSession();
	ASM::$pam->load(array('rColor' => CTR::$data->get('playerInfo')->get('color'), 'status' => PAM_CHIEF));

	if ($rCandidate == 0) {
		$rCandidate = ASM::$pam->get()->id());
	}

	if (ASM::$elm->size() > 0) {
		if (ASM::$cam->size() > 0 || ASM::$pam->get()->id == $rCandidate) {
			if (ASM::$elm->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
				$_VOM = ASM::$vom->getCurrentSession();
				ASM::$vom->newSession();
				ASM::$vom->load(array('rPlayer' => CTR::$data->get('playerId'), 'rElection' => $rElection));

				if (ASM::$vom->get() == 0) {
					$_CLM = ASM::$clm->getCurrentSession();
					ASM::$clm->newSession();
					ASM::$clm->load(array('id' => ASM::$elm->get()->rColor));

					if(ASM::$clm->get()->electionStatement == Color::ELECTION) {
						$vote = new Vote();
						$vote->rPlayer = CTR::$data->get('playerId');
						$vote->rCandidate = $rCandidate;
						$vote->rElection = $rElection;
						$vote->dVotation = Utils::now();
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
				CTR::$alert->add('Cette election ne se déroule pas dans votre faction.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Ce candidat n\'existe pas.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Cette election n\'existe pas.', ALERT_STD_ERROR);
	}

	ASM::$cam->changeSession($_CAM);
	ASM::$elm->changeSession($_ELM);
	ASM::$pam->changeSession($_PAM);
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}