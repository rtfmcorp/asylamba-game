<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;

$id 		= Utils::getHTTPData('id');
$content	= Utils::getHTTPData('content');
$title		= Utils::getHTTPData('title');

if ($title !== FALSE AND $content !== FALSE && $id !== FALSE) {	
	$S_FNM_1 = ASM::$fnm->getCurrentSession();
	ASM::$fnm->newSession();
	ASM::$fnm->load(array('id' => $id));

	if (ASM::$fnm->size() == 1) {
		if (CTR::$data->get('playerInfo')->get('status') >= 3) {
			ASM::$fnm->get()->title = $title;
			ASM::$fnm->get()->edit($content);
		} else {
			CTR::$alert->add('Vous n\'avez pas le droit pour créer une annonce.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Cette annonce n\'existe pas.', ALERT_STD_FILLFORM);
	}

	ASM::$fnm->changeSession($S_FNM_1);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}