<?php
include_once ATHENA;
# modify investments in university action

# int credit 		nouveau montant à investir

if (CTR::$get->exist('baseid')) {
	$baseId = CTR::$get->get('baseid');
} elseif (CTR::$post->exist('baseid')) {
	$baseId = CTR::$post->get('baseid');
} else {
	$baseId = FALSE;
}
if (CTR::$get->exist('credit')) {
	$credit = CTR::$get->get('credit');
} elseif (CTR::$post->exist('credit')) {
	$credit = CTR::$post->get('credit');
} else {
	$credit = FALSE;
}
if (CTR::$get->exist('category')) {
	$category = CTR::$get->get('category');
} elseif (CTR::$post->exist('category')) {
	$category = CTR::$post->get('category');
} else {
	$category = FALSE;
}

if ($credit !== FALSE) { 
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
	ASM::$pam->get()->iUniversity = $description;

	CTR::$alert->add('L\'investissement dans l\'université a été modifié', ALERT_STD_SUCCESS);

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour modifier cet investissement', ALERT_STD_FILLFORM);
}
?>