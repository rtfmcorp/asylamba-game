<?php
include_once DEMETER;
include_once ZEUS;
#rlaw	id de la loi
#choice le vote du joueur

$rLaw = Utils::getHTTPData('rlaw');
$choice = Utils::getHTTPData('choice');


if ($rLaw !== FALSE && $choice !== FALSE) {
	if (CTR::$data->get('playerInfo')->get('status') == PAM_PARLIAMENT) {
		$_LAM = ASM::$lam->getCurrentSession();
		ASM::$lam->newSession();
		ASM::$lam->load(array('id' => $rLaw));

		if (ASM::$lam->size() > 0) {
			if (ASM::$lam->get()->statement == Law::VOTATION) {
				$_VLM = ASM::$vlm->getCurrentSession();
				ASM::$vlm->newSession();
				ASM::$vlm->load(array('rPlayer' => CTR::$data->get('playerId'), 'rLaw' => $rLaw));

				if (ASM::$vlm->size() == 0) {
					$vote = new VoteLaw();
					$vote->rPlayer = CTR::$data->get('playerId');
					$vote->rLaw = $rLaw;
					$vote->vote = $choice;
					$vote->dVotation = Utils::now();
					ASM::$vlm->add($vote);
				} else {
					CTR::$alert->add('Vous avez déjà voté.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Cette loi est déjà votée.', ALERT_STD_ERROR);
			}
			ASM::$vlm->changeSession($_VLM);
		} else {
			CTR::$alert->add('Cette loi n\'existe pas.', ALERT_STD_ERROR);
		}

		ASM::$cam->changeSession($_LAM);
	} else {
		CTR::$alert->add('Vous n\'avez pas le droit de voter.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}