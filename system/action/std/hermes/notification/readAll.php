<?php
# read all notifications

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;

$S_NTM1 = ASM::$ntm->getCurrentSession();
ASM::$ntm->newSession(ASM_UMODE);
ASM::$ntm->load(array('rPlayer' => CTR::$data->get('playerId'), 'readed' => 0));

$nbNotifications = ASM::$ntm->size();

for ($i = 0; $i < $nbNotifications; $i++) {
	$notif = ASM::$ntm->get($i);
	$notif->setReaded(1);
}

if ($nbNotifications > 1) {
	CTR::$alert->add($nbNotifications . ' notifications ont été marquées comme lues.', ALERT_STD_SUCCESS);
} else if (ASM::$ntm->size() == 1) {
	CTR::$alert->add('Une notification a été marquée comme lue.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('Toutes vos notifications ont déjà été lues.', ALERT_STD_SUCCESS);
}

ASM::$ntm->changeSession($S_NTM1);