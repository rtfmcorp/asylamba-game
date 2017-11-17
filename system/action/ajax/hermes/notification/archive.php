<?php
# archive or unarchive action
# int id 			id de la notif

use Asylamba\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->request->get('id');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

if ($id) {
    $notification = $notificationManager->get($id);
    $notification->setArchived(!$notif->getArchived());
    $this->getContainer()->get('entity_manager')->flush($notification);
} else {
    throw new ErrorException('cette notification n\'existe pas');
}
