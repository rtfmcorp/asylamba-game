<?php
include_once ATHENA;
# modify investments in university action

# int credit 		nouveau montant à investir

if (CTR::$get->exist('credit')) {
	$credit = CTR::$get->get('credit');
} elseif (CTR::$post->exist('credit')) {
	$credit = CTR::$post->get('credit');
} else {
	$credit = FALSE;
}

if ($credit !== FALSE) { 
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
	ASM::$pam->get()->iUniversity = $credit;

	CTR::$alert->add('L\'investissement dans l\'université a été modifié', ALERT_STD_SUCCESS);

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour modifier cet investissement', ALERT_STD_FILLFORM);
}
?>