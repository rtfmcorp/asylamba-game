<?php
# delete all notifications

include_once ARES;

$S_REP1 = ASM::$rep->getCurrentSession();
ASM::$rep->newSession(ASM_UMODE);

$nbr = ASM::$rep->deleteByRPlayer(CTR::$data->get('playerId'));

if ($nbr > 1) {
	CTR::$alert->add($nbr . ' rapports ont été supprimés.', ALERT_STD_SUCCESS);
} else if ($nbr == 1) {
	CTR::$alert->add('Un rapport a été supprimé.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('Tous vos rapports ont déjà été supprimés.', ALERT_STD_SUCCESS);
}

ASM::$rep->changeSession($S_REP1);