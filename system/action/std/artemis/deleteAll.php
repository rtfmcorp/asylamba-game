<?php
# delete all notifications

use Asylamba\Classes\Library\Http\Response;

$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$spyReportManager = $this->getContainer()->get('artemis.spy_report_manager');

$S_SRM1 = $spyReportManager->getCurrentSession();
$spyReportManager->newSession(ASM_UMODE);
$spyReportManager->load(array('rPlayer' => $session->get('playerId')));

$nbr = $spyReportManager->deleteByRPlayer($session->get('playerId'));

if ($nbr > 1) {
	$response->flashbag->add($nbr . ' rapports ont été supprimés.', Response::FLASHBAG_SUCCESS);
} else if ($nbr == 1) {
	$response->flashbag->add('Un rapport a été supprimé.', Response::FLASHBAG_SUCCESS);
} else {
	$response->flashbag->add('Tous vos rapports ont déjà été supprimés.', Response::FLASHBAG_SUCCESS);
}

$spyReportManager->changeSession($S_SRM1);