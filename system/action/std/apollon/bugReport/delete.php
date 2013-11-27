<?php
include_once APOLLON;
# delete a bug report action

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
	ASM::$btm->get()->statement = BugTracker::ST_DELETED;
	ASM::$btm->changeSession($S_BTM1);

	CTR::$alert->add('Rapport d\'erreur supprimé.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('pas assez d\'informations pour supprimer un rapport d\'erreur', ALERT_STD_FILLFORM);
}
?>