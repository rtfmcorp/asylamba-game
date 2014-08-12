<?php
# int id 			id du rapport

include_once ARES;

$id = Utils::getHTTPData('id');


if ($id) {
	$S_RPM = ASM::$rpm->getCurrentSession();
	ASM::$rpm->newSession();
	ASM::$rpm->load(array('id' => $id));
	$report = ASM::$rpm->get();
	if (ASM::$rpm->size() == 1) {

		if ($report->rPlayerAttacker == CTR::$data->get('playerId')) {
			ASM::$rpm->get()->statementAttacker = Report::DELETED;
			CTR::$alert->add('Rapport supprimé', ALERT_STD_SUCCESS);
		} elseif ($report->rPlayerDefender == CTR::$data->get('playerId')) {
			ASM::$rpm->get()->statementDefender = Report::DELETED;
			CTR::$alert->add('Rapport supprimé', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('Ce rapport ne vous appartient pas', ALERT_STD_ERROR);
		}
		ASM::$rpm->changeSession($S_RPM);
	} else {
		CTR::$alert->add('Ce rapport n\'existe pas', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('veuillez indiquer le numéro du rapport', ALERT_STD_ERROR);
}