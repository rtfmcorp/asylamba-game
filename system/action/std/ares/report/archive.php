<?php
# archive or unarchive action
# int id 			id du rapport

use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$reportManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\ReportManager::class);

$id = $request->query->get('id');

if ($id) {
	if (($report = $reportManager->get($id)) !== null) {
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
} else {
	throw new ErrorException('Manque de précision sur le rapport.');
}
$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush();
