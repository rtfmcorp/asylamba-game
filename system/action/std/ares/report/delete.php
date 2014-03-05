<?php
# archive or unarchive action

# int id 			id du rapport

include_once ARES;

if (CTR::$get->exist('id')) {
	$id = CTR::$get->get('id');
} else if (CTR::$post->exist('id')) {
	$id = CTR::$post->get('id');
} else {
	$id = FALSE;
}

if ($id) {
	$S_REP1 = ASM::$rep->getCurrentSession();
	ASM::$rep->newSession(ASM_UMODE);
	ASM::$rep->load(array('id' => $id));
	$report = ASM::$rep->get();
	if ($report->rPlayerAttacker == CTR::$data->get('playerId')) {
		if ($report->rBigReportAttacker != 0) {
			ASM::$rep->deleteById($report->rBigReportAttacker);
			$report->rBigReportAttacker = 0;
			CTR::$alert->add('Rapport supprimé', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('Ce rapport est déjà supprimé', ALERT_STD_ERROR);
		}
	} else if ($report->rPlayerDefender == CTR::$data->get('playerId')) {
		if ($report->rBigReportAttacker != 0) {
			ASM::$rep->deleteById($report->rBigReportDefender);
			$report->rBigReportAttacker = 0;
			CTR::$alert->add('Rapport supprimé', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('Ce rapport est déjà supprimé', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Ce rapport ne vous appartient pas', ALERT_STD_ERROR);
	}
	ASM::$rep->changeSession($S_REP1);
} else {
	CTR::$alert->add('Ce rapport n\'existe pas', ALERT_STD_ERROR);
}