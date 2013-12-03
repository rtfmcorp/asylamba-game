<?php
include_once ZEUS;

if (CTR::$get->exist('bindkey')) {
	$S_PAM_1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('bind' => CTR::$get->get('bindkey')));

	if (ASM::$pam->size() == 1) {
		ASM::$pam->get()->setStatement(PAM_BANNED);

		echo serialize(array('statement' => 'success'));
	} else {
		echo serialize(array(
			'statement' => 'error',
			'message' => 'Joueur inconnu'
		));
	}

	ASM::$pam->changeSession($S_PAM_1);
} else {
	echo serialize(array(
		'statement' => 'error',
		'message' => 'Donnée manquante'
	));
}
?>