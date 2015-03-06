<?php
include_once DEMETER;

$content = Utils::getHTTPData('content');
$id = Utils::getHTTPData('id');

if ($content && $id) {	
		$_FMM = ASM::$fmm->getCurrentSession();
		ASM::$fmm->newSession();
		ASM::$fmm->load(array('id' => $id));

		if (ASM::$fmm->size() > 0) {
			if (CTR::$data->get('playerId') == ASM::$fmm->get()->rPlayer || CTR::$data->get('playerInfo')->get('status') > 2) {
				ASM::$fmm->get()->edit($content);
				ASM::$fmm->get()->dLastModification = Utils::now();
			} else {
				CTR::$alert->add('Pas les droits.', ALERT_STD_FILLFORM);
			}
		} else {
			CTR::$alert->add('message existe pas.', ALERT_STD_FILLFORM);
		}
		ASM::$fmm->changeSession($_FMM);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}