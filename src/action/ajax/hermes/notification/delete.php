<?php
# archive or unarchive action

# int id 			id de la notif

use App\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->request->get('id');
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);

if ($id) {
	$notification = $notificationManager->get($id);
	$entityManager->remove($notification);
	$entityManager->flush($notification);
} else {
	throw new ErrorException('Cette notification n\'existe pas');
}
