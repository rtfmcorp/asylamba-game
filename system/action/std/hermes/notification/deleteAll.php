<?php
# delete all notifications

use Asylamba\Classes\Library\Flashbag;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$session = $this->getContainer()->get('app.session');

$S_NTM1 = $notificationManager->getCurrentSession();
$notificationManager->newSession(ASM_UMODE);
$notificationManager->load(array('rPlayer' => $session->get('playerId')));

$nbr = $notificationManager->deleteByRPlayer($session->get('playerId'));

if ($nbr > 1) {
	$session->addFlashbag($nbr . ' notifications ont été supprimées.', Flashbag::TYPE_SUCCESS);
} else if ($nbr == 1) {
	$session->addFlashbag('Une notification a été supprimée.', Flashbag::TYPE_SUCCESS);
} else {
	$session->addFlashbag('Toutes vos notifications ont déjà été supprimées.', Flashbag::TYPE_SUCCESS);
}

$notificationManager->changeSession($S_NTM1);