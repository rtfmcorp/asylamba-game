<?php
include_once ATHENA;
# modify investments in university action

# int credit 		nouveau montant à investir

$credit = Utils::getHTTPData('credit');


if ($credit !== FALSE) { 
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
	ASM::$pam->get()->iUniversity = $credit;

	# tutorial
	if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
		switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
			case TutorialResource::MODIFY_UNI_INVEST:
				TutorialHelper::setStepDone();
				break;
		}
	}

	CTR::$alert->add('L\'investissement dans l\'université a été modifié', ALERT_STD_SUCCESS);

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour modifier cet investissement', ALERT_STD_FILLFORM);
}
?>