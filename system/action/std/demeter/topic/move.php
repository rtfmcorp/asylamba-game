<?php
include_once DEMETER;

$rForum = Utils::getHTTPData('rForum');
$id = Utils::getHTTPData('id');

if ($rForum && $id) {
	$_TOM = ASM::$tom->getCurrentSession();
	ASM::$tom->newSession();
	ASM::$tom->load(array('id' => $id));

	if (ASM::$tom->size() > 0) {
		if (CTR::$data->get('playerInfo')->get('status') > 2) {
			ASM::$tom->get()->rForum = $rForum;
			ASM::$tom->get()->dLastModification = Utils::now();
		} else {
			CTR::$alert->add('Pas les droits.', ALERT_STD_FILLFORM);
		}
	} else {
		CTR::$alert->add('truc existe pas.', ALERT_STD_FILLFORM);	
	}
	ASM::$tom->changeSession($_TOM);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}