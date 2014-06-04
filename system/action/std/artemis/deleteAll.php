<?php
# delete all notifications

include_once ARTEMIS;

$S_SRM1 = ASM::$srm->getCurrentSession();
ASM::$srm->newSession(ASM_UMODE);
ASM::$srm->load(array('rPlayer' => CTR::$data->get('playerId')));

$nbr = ASM::$srm->deleteByRPlayer(CTR::$data->get('playerId'));

if ($nbr > 1) {
	CTR::$alert->add($nbr . ' rapports ont été supprimés.', ALERT_STD_SUCCESS);
} else if ($nbr == 1) {
	CTR::$alert->add('Un rapport a été supprimé.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('Tous vos rapports ont déjà été supprimés.', ALERT_STD_SUCCESS);
}

ASM::$srm->changeSession($S_SRM1);