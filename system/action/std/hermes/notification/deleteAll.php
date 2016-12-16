<?php
# delete all notifications

use Asylamba\Classes\Library\Http\Response;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');

$S_NTM1 = $notificationManager->getCurrentSession();
$notificationManager->newSession(ASM_UMODE);
$notificationManager->load(array('rPlayer' => $session->get('playerId')));

$nbr = $notificationManager->deleteByRPlayer($session->get('playerId'));

if ($nbr > 1) {
	$response->flashbag->add($nbr . ' notifications ont été supprimées.', Response::FLASHBAG_SUCCESS);
} else if ($nbr == 1) {
	$response->flashbag->add('Une notification a été supprimée.', Response::FLASHBAG_SUCCESS);
} else {
	$response->flashbag->add('Toutes vos notifications ont déjà été supprimées.', Response::FLASHBAG_SUCCESS);
}

$notificationManager->changeSession($S_NTM1);