<?php
# int id 			id du rapport

use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('session_wrapper');
$reportManager = $this->getContainer()->get('ares.report_manager');

$id = $request->query->get('id');

if ($id) {
    if (($report = $reportManager->get($id)) !== null) {
        if ($report->rPlayerAttacker == $session->get('playerId')) {
            $report->statementAttacker = Report::DELETED;
            $response->redirect('fleet/view-archive');
        } elseif ($report->rPlayerDefender == $session->get('playerId')) {
            $report->statementDefender = Report::DELETED;
            $response->redirect('fleet/view-archive');
        } else {
            throw new ErrorException('Ce rapport ne vous appartient pas');
        }
    } else {
        throw new ErrorException('Ce rapport n\'existe pas');
    }
} else {
    throw new ErrorException('veuillez indiquer le numéro du rapport');
}
$this->getContainer()->get('entity_manager')->flush();
