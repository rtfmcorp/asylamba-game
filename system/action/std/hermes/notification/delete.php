<?php
# archive or unarchive action

# int id 			id de la notif

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;

$id = Utils::getHTTPData('id');


if ($id) {
	$S_NTM1 = ASM::$ntm->getCurrentSession();
	ASM::$ntm->newSession(ASM_UMODE);
	ASM::$ntm->load(array('id' => $id));
	if (ASM::$ntm->size() == 1 && ASM::$ntm->get()->rPlayer == CTR::$data->get('playerId')) {
		ASM::$ntm->deleteById($id);	
		CTR::$alert->add('Notification supprimée', ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('C\'est pas très bien de supprimer les notifications des autres.', ALERT_STD_ERROR);
	}
	ASM::$ntm->changeSession($S_NTM1);
} else {
	CTR::$alert->add('Cette notification n\'existe pas', ALERT_STD_ERROR);
}