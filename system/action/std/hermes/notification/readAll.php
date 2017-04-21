<?php
# read all notifications

use Asylamba\Classes\Library\Flashbag;

use Asylamba\Modules\Hermes\Model\Notification;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$session = $this->getContainer()->get('app.session');

$notifications = $notificationManager->getUnreadNotifications($session->get('playerId'));
$nbNotifications = count($notifications);

foreach ($notifications as $notification) {
	$notification->setReaded(1);
}

$this->getContainer()->get('entity_manager')->flush(Notification::class);

if ($nbNotifications > 1) {
	$session->addFlashbag($nbNotifications . ' notifications ont été marquées comme lues.', Flashbag::TYPE_SUCCESS);
} else if ($nbNotifications == 1) {
	$session->addFlashbag('Une notification a été marquée comme lue.', Flashbag::TYPE_SUCCESS);
} else {
	$session->addFlashbag('Toutes vos notifications ont déjà été lues.', Flashbag::TYPE_SUCCESS);
}