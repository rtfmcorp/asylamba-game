<?php
# read all notifications

use Asylamba\Classes\Library\Http\Response;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');

$S_NTM1 = $notificationManager->getCurrentSession();
$notificationManager->newSession(ASM_UMODE);
$notificationManager->load(array('rPlayer' => $session->get('playerId'), 'readed' => 0));

$nbNotifications = $notificationManager->size();

for ($i = 0; $i < $nbNotifications; $i++) {
	$notif = $notificationManager->get($i);
	$notif->setReaded(1);
}

if ($nbNotifications > 1) {
	$response->flashbag->add($nbNotifications . ' notifications ont été marquées comme lues.', Response::FLASHBAG_SUCCESS);
} else if ($notificationManager->size() == 1) {
	$response->flashbag->add('Une notification a été marquée comme lue.', Response::FLASHBAG_SUCCESS);
} else {
	$response->flashbag->add('Toutes vos notifications ont déjà été lues.', Response::FLASHBAG_SUCCESS);
}

$notificationManager->changeSession($S_NTM1);