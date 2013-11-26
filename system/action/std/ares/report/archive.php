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
	if (ASM::$rep->size() > 0) {
		$report = ASM::$rep->get();
		if (CTR::$data->get('playerId') == $report->rPlayerAttacker) {
			if ($report->archivedAttacker == 0) {
				$report->archivedAttacker = 1;
				CTR::$alert->add('rapport archivé.', ALERT_STD_SUCCESS);
			} else {
				$report->archivedAttacker = 0;
				CTR::$alert->add('rapport désarchivé.', ALERT_STD_SUCCESS);
			}
		} else if (CTR::$data->get('playerId') == $report->rPlayerDefender) {
			if ($report->archivedDefender == 0) {
				$report->archivedDefender = 1;
				CTR::$alert->add('rapport archivé.', ALERT_STD_SUCCESS);
			} else {
				$report->archivedDefender = 0;
				CTR::$alert->add('rapport désarchivé.', ALERT_STD_SUCCESS);
			}
		} else {
		CTR::$alert->add('Ce rapport ne vous appartient pas.', ALERT_STD_ERROR);

		}
	} else {
		CTR::$alert->add('Ce rapport n\'existe pas.', ALERT_STD_ERROR);
	}
	ASM::$rep->changeSession($S_REP1);
} else {
	CTR::$alert->add('Manque de précision sur le rapport.', ALERT_STD_ERROR);
}
