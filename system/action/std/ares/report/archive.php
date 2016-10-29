<?php
# archive or unarchive action
# int id 			id du rapport

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Ares\Model\Report;

$id = Utils::getHTTPData('id');

if ($id) {
	$S_LRM = ASM::$lrm->getCurrentSession();
	ASM::$lrm->newSession();
	ASM::$lrm->load(array('r.id' => $id));

	if (ASM::$lrm->size() > 0) {
		$report = ASM::$lrm->get();
		
		if (CTR::$data->get('playerId') == $report->rPlayerAttacker) {
			if ($report->statementAttacker == Report::STANDARD) {
				$report->statementAttacker = Report::ARCHIVED;
			} else {
				$report->statementAttacker = Report::STANDARD;
			}
		} else if (CTR::$data->get('playerId') == $report->rPlayerDefender) {
			if ($report->statementDefender == Report::STANDARD) {
				$report->statementDefender = Report::ARCHIVED;
			} else {
				$report->statementDefender = Report::STANDARD;
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
