<?php
# archive or unarchive action

# int id 			id du rapport

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('session_wrapper');
$spyReportManager = $this->getContainer()->get('artemis.spy_report_manager');

$id = $request->query->get('id');

if ($id) {
	$S_SRM1 = $spyReportManager->getCurrentSession();
	$spyReportManager->newSession();
	$spyReportManager->load(array('id' => $id));
	$report = $spyReportManager->get();
	if ($spyReportManager->size() == 1) {

		if ($report->rPlayer == $session->get('playerId')) {
			$spyReportManager->deleteById($report->id);
			$session->addFlashbag('Rapport d\'espionnage supprimé', Flashbag::TYPE_SUCCESS);
			$response->redirect('fleet/view-spyreport');
		} else {
			throw new ErrorException('Ce rapport ne vous appartient pas');
		}
	} else {
		throw new ErrorException('Ce rapport n\'existe pas');
	}
	$spyReportManager->changeSession($S_SRM1);
} else {
	throw new ErrorException('veuillez indiquer le numéro du rapport');
}