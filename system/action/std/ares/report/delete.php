<?php
# int id 			id du rapport

use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$littleReportManager = $this->getContainer()->get('ares.little_report_manager');

$id = $request->query->get('id');

if ($id) {
	$S_LRM = $littleReportManager->getCurrentSession();
	$littleReportManager->newSession();
	$littleReportManager->load(array('r.id' => $id));

	if ($littleReportManager->size() > 0) {
		$report = $littleReportManager->get();

		if ($report->rPlayerAttacker == $session->get('playerId')) {
			$littleReportManager->get()->statementAttacker = Report::DELETED;
			$response->redirect('fleet/view-archive');
		} elseif ($report->rPlayerDefender == $session->get('playerId')) {
			$littleReportManager->get()->statementDefender = Report::DELETED;
			$response->redirect('fleet/view-archive');
		} else {
			throw new ErrorException('Ce rapport ne vous appartient pas');
		}
	} else {
		throw new ErrorException('Ce rapport n\'existe pas');
	}

	$littleReportManager->changeSession($S_LRM);	
} else {
	throw new ErrorException('veuillez indiquer le numéro du rapport');
}