<?php
include_once HERMES;
# read notification action

# int notif 		notif id

$notif = Utils::getHTTPData('notif');


if ($notif !== FALSE) {
	$S_NTM1 = ASM::$ntm->getCurrentSession();
	ASM::$ntm->newSession();
	ASM::$ntm->load(array('id' => $notif, 'rPlayer' => CTR::$data->get('playerId')));

	if (ASM::$ntm->size() == 1) {
		ASM::$ntm->get()->setReaded(1);
	} else {
		CTR::$alert->add('Cette notification ne vous appartient pas', ALERT_STD_FILLFORM);
	}

	ASM::$ntm->changeSession($S_NTM1);
} else {
	CTR::$alert->add('Erreur dans la requête AJAX', ALERT_STD_FILLFORM);
}
?>