<?php
# archive or unarchive action
# int id 			id du rapport

use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$littleReportManager = $this->getContainer()->get('ares.little_report_manager');

$id = $request->query->get('id');

if ($id) {
	$S_LRM = $littleReportManager->getCurrentSession();
	$littleReportManager->newSession();
	$littleReportManager->load(array('r.id' => $id));

	if ($littleReportManager->size() > 0) {
		$report = $littleReportManager->get();
		
		if ($session->get('playerId') == $report->rPlayerAttacker) {
			if ($report->statementAttacker == Report::STANDARD) {
				$report->statementAttacker = Report::ARCHIVED;
			} else {
				$report->statementAttacker = Report::STANDARD;
			}
		} else if ($session->get('playerId') == $report->rPlayerDefender) {
			if ($report->statementDefender == Report::STANDARD) {
				$report->statementDefender = Report::ARCHIVED;
			} else {
				$report->statementDefender = Report::STANDARD;
			}
		} else {
		throw new ErrorException('Ce rapport ne vous appartient pas.');

		}
	} else {
		throw new ErrorException('Ce rapport n\'existe pas.');
	}

	$littleReportManager->changeSession($S_LRM);
} else {
	throw new ErrorException('Manque de précision sur le rapport.');
}
