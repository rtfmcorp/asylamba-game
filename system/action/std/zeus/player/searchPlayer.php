<?php
include_once ZEUS;
# search player profile

# string name 	nom du joueur
$name = Utils::getHTTPData('name');

# input protection
$p = new Parser();
$name = $p->protect($name);

if ($name !== FALSE AND $name !== '') {
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('name' => $name));
	
	if (ASM::$pam->size() == 1) {
		CTR::redirect('diary/player-' . ASM::$pam->get()->getId());
	} else {
		CTR::$alert->add('Aucun joueur ne correspond à votre recherche.', ALERT_STD_ERROR);
	}

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour chercher un joueur', ALERT_STD_FILLFORM);
}
?>