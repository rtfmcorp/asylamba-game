<?php
include_once DEMETER;

$id 		= Utils::getHTTPData('id');

if ($id !== FALSE) {	
	$S_FNM_1 = ASM::$fnm->getCurrentSession();
	ASM::$fnm->newSession();
	ASM::$fnm->load(array('id' => $id));

	if (ASM::$fnm->size() == 1) {
		ASM::$fnm->deleteById($id);

		CTR::$alert->add('L\'annonce a bien été supprimée.', ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('Cette annonce n\'existe pas.', ALERT_STD_FILLFORM);
	}

	ASM::$fnm->changeSession($S_FNM_1);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}