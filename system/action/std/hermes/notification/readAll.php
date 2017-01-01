<?php
# read all notifications

use Asylamba\Classes\Library\Flashbag;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
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
	$session->addFlashbag($nbNotifications . ' notifications ont été marquées comme lues.', Flashbag::TYPE_SUCCESS);
} else if ($notificationManager->size() == 1) {
	$session->addFlashbag('Une notification a été marquée comme lue.', Flashbag::TYPE_SUCCESS);
} else {
	$session->addFlashbag('Toutes vos notifications ont déjà été lues.', Flashbag::TYPE_SUCCESS);
}

$notificationManager->changeSession($S_NTM1);