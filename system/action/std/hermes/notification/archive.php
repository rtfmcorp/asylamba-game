<?php
# archive or unarchive action
# int id 			id de la notif

use Asylamba\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->query->get('id');

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$session = $this->getContainer()->get('app.session');

if ($id) {
	if (($notification = $notificationManager->get($id)) !== null && $notification->rPlayer === $session->get('playerId')) {
		$notification->setArchived($notification->getArchived());
		$this->getContainer()->get('entity_manager')->flush($notification);
	} else {
		throw new ErrorException('Ce n\'est pas bien d\'archiver les notifications des autres.');
	}
} else {
	throw new ErrorException('cette notification n\'existe pas');
}
