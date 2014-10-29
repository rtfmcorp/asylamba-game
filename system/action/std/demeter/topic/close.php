<?php
include_once DEMETER;

$id = Utils::getHTTPData('id');

if ($id !== FALSE) {
	$S_TOM = ASM::$tom->getCurrentSession();
	ASM::$tom->newSession();
	ASM::$tom->load(array('id' => $id));

	if (ASM::$tom->size() == 1) {
		if (CTR::$data->get('playerInfo')->get('status') > 2) {
			if (ASM::$tom->get()->isClosed == 1) {
				ASM::$tom->get()->isClosed = 0;
			} else {
				ASM::$tom->get()->isClosed = 1;
			}
			CTR::$alert->add('Le sujet a bien été fermé/ouvert', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('Vous n\'avez pas les droits', ALERT_STD_FILLFORM);	
		}
	} else {
		CTR::$alert->add('Ce sujet n\'existe pas', ALERT_STD_FILLFORM);
	}

	ASM::$tom->changeSession($S_TOM);
} else {
	CTR::$alert->add('Manque d\'information', ALERT_STD_FILLFORM);
}