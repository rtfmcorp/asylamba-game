<?php
include_once ZEUS;

$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession();
ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

if (ASM::$pam->size() == 1) {

	# sending email API call
	$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
	$success = $api->abandonServer(ASM::$pam->get()->bind, APP_ID);

	if ($success) {

		ASM::$pam->get()->bind = ASM::$pam->get()->bind . 'ABANDON';
		ASM::$pam->get()->statement = PAM_DELETED;
		
		# clean session
		CTR::$data->destroy();
		CTR::redirect(GETOUT_ROOT . 'serveurs', TRUE);

	} else {
		CTR::$alert->add('Une erreur s\'est produite sur le portail. Contactez un administrateur pour résoudre ce problème.', ALERT_STD_ERROR);
	}

} else {
	CTR::$alert->add('Une erreur s\'est produite. Contactez un administrateur pour résoudre ce problème.', ALERT_STD_ERROR);
}

ASM::$pam->changeSession($S_PAM1);


?>
