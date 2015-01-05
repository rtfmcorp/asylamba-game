<?php
# archive or unarchive action
# int id 			id de la notif

include_once HERMES;

$id = Utils::getHTTPData('id');


if ($id) {
	$S_NTM1 = ASM::$ntm->getCurrentSession();
	ASM::$ntm->newSession(ASM_UMODE);
	ASM::$ntm->load(array('id' => $id));
	if (ASM::$ntm->size() == 1 && ASM::$ntm->get()->rPlayer == CTR::$data->get('playerId')) {
		$notif = ASM::$ntm->get();
		if ($notif->getArchived() == 0) {
			$notif->setArchived(1);
		} else {
			$notif->setArchived(0);
		}
	} else {
		CTR::$alert->add('Ce n\'est pas bien d\'archiver les notifications des autres.', ALERT_STD_ERROR);
	}
	ASM::$ntm->changeSession($S_NTM1);
} else {
	CTR::$alert->add('cette notification n\'existe pas', ALERT_STD_ERROR);
}
