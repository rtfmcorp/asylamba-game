<?php
# int id 			id du rapport

include_once ARES;

$id = Utils::getHTTPData('id');


if ($id) {
	$S_LRM = ASM::$lrm->getCurrentSession();
	ASM::$lrm->newSession();
	ASM::$lrm->load(array('id' => $id));
	$report = ASM::$lrm->get();
	if (ASM::$lrm->size() > 0) {

		if ($report->rPlayerAttacker == CTR::$data->get('playerId')) {
			ASM::$lrm->get()->statementAttacker = Report::DELETED;
		} elseif ($report->rPlayerDefender == CTR::$data->get('playerId')) {
			ASM::$lrm->get()->statementDefender = Report::DELETED;
		} else {
			CTR::$alert->add('Ce rapport ne vous appartient pas', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Ce rapport n\'existe pas', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('veuillez indiquer le numéro du rapport', ALERT_STD_ERROR);
}