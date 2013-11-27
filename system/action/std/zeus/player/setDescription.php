<?php
include_once ZEUS;
# set player description action

# string description 	description du joueur

if (CTR::$get->exist('description')) {
	$description = CTR::$get->get('description');
} elseif (CTR::$post->exist('description')) {
	$description = CTR::$post->get('description');
} else {
	$description = FALSE;
}

// protection des inputs
$p = new Parser();
$description = $p->protect($description);

if ($description !== FALSE AND $description !== '') {
	if (strlen($description) <= 1000) {
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		ASM::$pam->get()->setDescription($description);

		CTR::$alert->add('Description mise à jour', ALERT_STD_SUCCESS);

		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::$alert->add('description trop longue, limitez-vous à 1000 caractères', ALERT_STD_FILLFORM);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour changer la description', ALERT_STD_FILLFORM);
}
?>