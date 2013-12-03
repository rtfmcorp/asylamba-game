<?php
# delete all notifications

include_once HERMES;

$S_NTM1 = ASM::$ntm->getCurrentSession();
ASM::$ntm->newSession(ASM_UMODE);
ASM::$ntm->load(array('rPlayer' => CTR::$data->get('playerId')));

$nbr = ASM::$ntm->deleteByRPlayer(CTR::$data->get('playerId'));

if ($nbr > 1) {
	CTR::$alert->add($nbr . ' notifications ont été supprimées.', ALERT_STD_SUCCESS);
} else if ($nbr == 1) {
	CTR::$alert->add('Une notification a été supprimée.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('Toutes vos notifications ont déjà été supprimées.', ALERT_STD_SUCCESS);
}

ASM::$ntm->changeSession($S_NTM1);