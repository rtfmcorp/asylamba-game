<?php
include_once APOLLON;
# archive a bug report action

# int id 		id du rapport de bug

if (CTR::$get->exist('id')) {
	$id = CTR::$get->get('id');
} elseif (CTR::$post->exist('id')) {
	$id = CTR::$post->get('id');
} else {
	$id = FALSE;
}

if ($id !== FALSE) {
	$S_BTM1 = ASM::$btm->getCurrentSession();
	ASM::$btm->newSession(ASM_UMODE);
	ASM::$btm->load(array('id' => $id));
	ASM::$btm->get()->statement = BugTracker::ST_ARCHIVED;
	ASM::$btm->changeSession($S_BTM1);

	CTR::$alert->add('Rapport d\'erreur archivé.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('pas assez d\'informations pour archiver un rapport d\'erreur', ALERT_STD_FILLFORM);
}
?>