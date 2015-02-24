<?php
# vérification du joueur
# ajout des informations dans le managers
include_once ZEUS;

# extraction du bindkey
$query  = Security::uncrypt(CTR::$get->get('bindkey'), KEY_SERVER);
$bindkey= Security::extractBindkey($query);
$time 	= Security::extractTime($query);

# vérification de la validité du bindkey
if (abs((int)$time - time()) > 300) {
	header('Location: ' . GETOUT_ROOT . 'profil');
	exit();
}

$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession();
ASM::$pam->load(array('bind' => $bindkey, 'statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

if (ASM::$pam->size() == 1) {
	$player = ASM::$pam->get();
	$player->setStatement(PAM_ACTIVE);

	CTR::$data->initLastUpdate();
	CTR::$data->add('token', Utils::generateString(5));

	include_once CONNECTION . '/create-session.php';

	# mise de dLastConnection + dLastActivity
	$player->setDLastConnection(Utils::now());
	$player->setDLastActivity(Utils::now());

	# confirmation au portail
	if (APIMODE) {
		$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
		$api->confirmConnection($bindkey, APP_ID);
	}

	# redirection vers page de départ
	if (CTR::$get->equal('mode', 'splash')) {
		CTR::redirect('profil/mode-splash');
	} else {
		CTR::redirect('profil');
	}
} else { 
	header('Location: ' . GETOUT_ROOT . 'profil');
	exit();
}

ASM::$pam->changeSession($S_PAM1);
?>