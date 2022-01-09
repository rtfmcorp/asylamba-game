<?php
# read all notifications

use App\Classes\Library\Flashbag;

use App\Modules\Hermes\Model\Notification;

$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$notifications = $notificationManager->getUnreadNotifications($session->get('playerId'));
$nbNotifications = count($notifications);

foreach ($notifications as $notification) {
	$notification->setReaded(1);
}

$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush(Notification::class);

if ($nbNotifications > 1) {
	$session->addFlashbag($nbNotifications . ' notifications ont été marquées comme lues.', Flashbag::TYPE_SUCCESS);
} else if ($nbNotifications == 1) {
	$session->addFlashbag('Une notification a été marquée comme lue.', Flashbag::TYPE_SUCCESS);
} else {
	$session->addFlashbag('Toutes vos notifications ont déjà été lues.', Flashbag::TYPE_SUCCESS);
}
