<?php
# archive or unarchive action
# int id 			id du rapport

include_once ARES;

$id = Utils::getHTTPData('id');


if ($id) {
	$S_LRM = ASM::$lrm->getCurrentSession();
	ASM::$lrm->newSession();
	ASM::$lrm->load(array('id' => $id));
	if (ASM::$lrm->size() > 0) {
		$report = ASM::$lrm->get();
		if (CTR::$data->get('playerId') == $report->rPlayerAttacker) {
			if ($report->archivedAttacker == Report::STANDARD) {
				$report->archivedAttacker = Report::ARCHIVE;
			} else {
				$report->archivedAttacker = Report::STANDARD;
			}
		} else if (CTR::$data->get('playerId') == $report->rPlayerDefender) {
			if ($report->archivedDefender == Report::STANDARD) {
				$report->archivedDefender = Report::ARCHIVE;
			} else {
				$report->archivedDefender = Report::STANDARD;
			}
		} else {
		CTR::$alert->add('Ce rapport ne vous appartient pas.', ALERT_STD_ERROR);

		}
	} else {
		CTR::$alert->add('Ce rapport n\'existe pas.', ALERT_STD_ERROR);
	}
	ASM::$lrm->changeSession($S_LRM);
} else {
	CTR::$alert->add('Manque de précision sur le rapport.', ALERT_STD_ERROR);
}
