<?php
# delete all notifications

use Asylamba\Classes\Library\Flashbag;

$session = $this->getContainer()->get('session_wrapper');
$spyReportManager = $this->getContainer()->get('artemis.spy_report_manager');

$S_SRM1 = $spyReportManager->getCurrentSession();
$spyReportManager->newSession(ASM_UMODE);
$spyReportManager->load(array('rPlayer' => $session->get('playerId')));

$nbr = $spyReportManager->deleteByRPlayer($session->get('playerId'));

if ($nbr > 1) {
	$session->addFlashbag($nbr . ' rapports ont été supprimés.', Flashbag::TYPE_SUCCESS);
} else if ($nbr == 1) {
	$session->addFlashbag('Un rapport a été supprimé.', Flashbag::TYPE_SUCCESS);
} else {
	$session->addFlashbag('Tous vos rapports ont déjà été supprimés.', Flashbag::TYPE_SUCCESS);
}

$spyReportManager->changeSession($S_SRM1);