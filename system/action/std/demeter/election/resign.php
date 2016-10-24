<?php
#rplayer	id du joueur

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;

if (CTR::$data->get('playerInfo')->get('status') > PAM_PARLIAMENT && CTR::$data->get('playerInfo')->get('status') < PAM_CHIEF) {
	$_PAM = ASM::$pam->getCurrentsession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	if (ASM::$pam->size() > 0) {
		ASM::$pam->get()->status = PAM_PARLIAMENT;
		CTR::$data->get('playerInfo')->add('status', PAM_PARLIAMENT);
		CTR::$alert->add('Vous n\'êtes plus membre du gouvernement.', ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('Ce joueur n\'existe pas.', ALERT_STD_ERROR);
	}

	ASM::$pam->changeSession($_PAM);
} else {
	CTR::$alert->add('Vous n\'êtes pas dans le gouvernement de votre faction ou en êtes le chef.', ALERT_STD_ERROR);
}