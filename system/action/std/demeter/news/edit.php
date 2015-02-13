<?php
include_once DEMETER;

$content = Utils::getHTTPData('content');
$id = Utils::getHTTPData('id');

if ($content && $id) {	
		$_FNM = ASM::$fnm->getCurrentSession();
		ASM::$fnm->newSession();
		ASM::$fnm->load(array('id' => $id));

		if (ASM::$fnm->size() > 0) {
			if (CTR::$data->get('playerInfo')->get('status') >= 3) {
				ASM::$fnm->get()->edit($content);
			} else {
				CTR::$alert->add('Pas les droits.', ALERT_STD_FILLFORM);
			}
		} else {
			CTR::$alert->add('message existe pas.', ALERT_STD_FILLFORM);	
		}
		ASM::$fnm->changeSession($_FNM);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}