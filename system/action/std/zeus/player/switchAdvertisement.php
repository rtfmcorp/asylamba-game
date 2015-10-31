<?php
include_once ZEUS;
# switch advertisement action

$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(ASM_UMODE);
ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

if (ASM::$pam->size() == 1) {
	$p = ASM::$pam->get();

	if ($p->premium == 0) {
		$p->premium = 1;
		CTR::$data->get('playerInfo')->add('premium', 1);
		CTR::$alert->add('Publicité déactivée. Vous êtes vraiment sûr ? Allez, re-cliquez un coup, c\'est cool les pubs.', ALERT_STD_SUCCESS);
	} else {
		$p->premium = 0;
		CTR::$data->get('playerInfo')->add('premium', 0);
		CTR::$alert->add('Publicitées activées. Merci beaucoup pour votre soutien. Je vous aime.', ALERT_STD_SUCCESS);
	}
} else {
	CTR::$alert->add('petit bug là, contactez un administrateur rapidement sous risque que votre ordinateur explose', ALERT_STD_ERROR);
}
ASM::$pam->changeSession($S_PAM1);
	

