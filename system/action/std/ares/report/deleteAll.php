<?php

use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get('app.session');
$liveReportManager = $this->getContainer()->get('ares.live_report_manager');

$reports = $liveReportManager->getPlayerReports($session->get('playerId'));

foreach ($reports as $report) {
	if ($report->rPlayerAttacker == $session->get('playerId')) {
		$report->statementAttacker = Report::DELETED;
	} elseif ($report->rPlayerDefender == $session->get('playerId')) {
		$report->statementDefender = Report::DELETED;
	} else {
		throw new ErrorException('Ces rapport ne vous appartient pas');
	}
}

$this->getContainer()->get('entity_manager')->flush();