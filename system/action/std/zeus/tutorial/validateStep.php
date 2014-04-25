<?php
include_once ZEUS;
# validate tutorial step action

$player = CTR::$data->get('playerId');
$stepTutorial = CTR::$data->get('playerInfo')->get('stepTutorial');
$stepDone = CTR::$data->get('playerInfo')->get('stepDone');

/*if ($stepDone AND $name !== '') {


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
}*/
?>