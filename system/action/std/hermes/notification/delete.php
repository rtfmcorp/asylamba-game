<?php
# archive or unarchive action

# int id 			id de la notif

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->query->get('id');

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$session = $this->getContainer()->get('session_wrapper');
$entityManager = $this->getContainer()->get('entity_manager');

if ($id) {
	if (($notification = $notificationManager->get($id)) !== null && $notification->rPlayer === $session->get('playerId')) {
		$entityManager->remove($notification);
		$entityManager->flush($notification);
		$session->addFlashbag('Notification supprimée', Flashbag::TYPE_SUCCESS);
	} else {
		throw new ErrorException('C\'est pas très bien de supprimer les notifications des autres.');
	}
} else {
	throw new ErrorException('Cette notification n\'existe pas');
}