<?php
#rplayer	id du joueur
#relection id election
#program
#chiefchoice
#treasurerchoice
#warlordchoice
#ministerchoice

$rElection = Utils::getHTTPData('relection');
$program = Utils::getHTTPData('program');
$chiefChoice = Utils::getHTTPData('chiefchoice');
$treasurerChoice = Utils::getHTTPData('treasurerchoice');
$warlordChoice = Utils::getHTTPData('warlordchoice');
$ministerChoice = Utils::getHTTPData('ministerchoice');

include_once DEMETER;
include_once ZEUS;

if ($rElection !== FALSE && $program !== FALSE) {
	$_ELM = ASM::$elm->getCurrentSession();
	ASM::$elm->newSession();
	ASM::$elm->load(array('id' => $rElection));

	if (ASM::$elm->size() > 0) {
		if (ASM::$elm->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
			if (in_array(ASM::$elm->get()->rColor, array(1, 2, 3, 4))) {
				$chiefChoice = 1;
				$treasurerChoice = 1;
				$warlordChoice = 1;
				$ministerChoice = 1;
			}
			if (CTR::$data->get('playerInfo')->get('status') > PAM_STANDARD) {
				$_CLM = ASM::$clm->getCurrentSession();
				ASM::$clm->newSession();
				ASM::$clm->load(array('id' => CTR::$data->get('playerInfo')->get('color')));
				$_CAM = ASM::$cam->getCurrentSession();
				ASM::$cam->newSession();
				ASM::$cam->load(array('rPlayer' => CTR::$data->get('playerId'), 'rElection' => $rElection));

				if(ASM::$clm->get()->electionStatement == Color::CAMPAIGN) {
					if ($chiefChoice !== NULL && $treasurerChoice !== FALSE && $warlordChoice !== FALSE && $ministerChoice !== FALSE) {
						if (ASM::$cam->size() == 0) {
							$candidate = new candidate();
							$candidate->rElection = $rElection;
							$candidate->rPlayer = CTR::$data->get('playerId');
							$candidate->chiefChoice = $chiefChoice;
							$candidate->treasurerChoice = $treasurerChoice;
							$candidate->warlordChoice = $warlordChoice;
							$candidate->ministerChoice = $ministerChoice;
							$candidate->dPresentation = Utils::now();
							$candidate->program = $program; 
							ASM::$cam->add($candidate);

							CTR::$alert->add('Candidature déposée.', ALERT_STD_SUCCESS);
						} else {
							ASM::$cam->deleteById(ASM::$cam->get()->getId());
							CTR::$alert->add('Candidature retirée.', ALERT_STD_SUCCESS);
						}
					} else {
						CTR::$alert->add('Informations manquantes sur les choix.', ALERT_STD_ERROR);	
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
		CTR::$alert->add('Cette election n\'existe pas.', ALERT_STD_ERROR);
	}
	ASM::$elm->changeSession($_ELM);
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}