<?php
# archive or unarchive action

# int id 			id de la notif

include_once HERMES;

if (CTR::$get->exist('id')) {
	$id = CTR::$get->get('id');
} else if (CTR::$post->exist('id')) {
	$id = CTR::$post->get('id');
} else {
	$id = FALSE;
}

if ($id) {
	$S_NTM1 = ASM::$ntm->getCurrentSession();
	ASM::$ntm->newSession(ASM_UMODE);
	ASM::$ntm->load(array('id' => $id));
	ASM::$ntm->deleteById($id);	
	ASM::$ntm->changeSession($S_NTM1);
} else {
	CTR::$alert->add('Cette notification n\'existe pas', ALERT_STD_ERROR);
}