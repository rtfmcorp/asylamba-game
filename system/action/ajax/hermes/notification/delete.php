<?php
# archive or unarchive action

# int id 			id de la notif

use Asylamba\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->request->get('id');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$entityManager = $this->getContainer()->get('entity_manager');

if ($id) {
    $notification = $notificationManager->get($id);
    $entityManager->remove($notification);
    $entityManager->flush($notification);
} else {
    throw new ErrorException('Cette notification n\'existe pas');
}
