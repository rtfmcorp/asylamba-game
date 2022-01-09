<?php
# archive or unarchive action
# int id 			id de la notif

use App\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->request->get('id');
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);

if ($id) {
	$notification = $notificationManager->get($id);
	$notification->setArchived(!$notif->getArchived());
	$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($notification);
} else {
	throw new ErrorException('cette notification n\'existe pas');
}
