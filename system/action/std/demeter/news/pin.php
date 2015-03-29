<?php
include_once DEMETER;

$id 		= Utils::getHTTPData('id');

if ($id !== FALSE) {
	$S_FNM_1 = ASM::$fnm->getCurrentSession();
	ASM::$fnm->newSession();
	ASM::$fnm->load(array('id' => $id));

	if (ASM::$fnm->size() == 1) {
		# chargement de toutes les factions
		$S_FNM_2 = ASM::$fnm->getCurrentSession();
		ASM::$fnm->newSession();
		ASM::$fnm->load(['rFaction' => CTR::$data->get('playerInfo')->get('color')]);

		for ($i = 0; $i < ASM::$fnm->size(); $i++) { 
			if (ASM::$fnm->get($i)->id == $id) {
				ASM::$fnm->get($i)->pinned = TRUE;
			} else {
				ASM::$fnm->get($i)->pinned = FALSE;
			}
		}

		ASM::$fnm->changeSession($S_FNM_2);
	} else {
		CTR::$alert->add('Cette annonce n\'existe pas.', ALERT_STD_FILLFORM);	
	}

	ASM::$fnm->changeSession($S_FNM_1);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}