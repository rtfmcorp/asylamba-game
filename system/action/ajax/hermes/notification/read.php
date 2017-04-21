<?php
# read notification action

# int notif 		notif id


use Asylamba\Classes\Exception\FormException;

$id = $this->getContainer()->get('app.request')->query->get('notif');

if ($id === null) {
	throw new FormException('Erreur dans la requête AJAX');
}
$session = $this->getContainer()->get('app.session');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

if (($notification = $notificationManager->get($id)) !== null && $notification->rPlayer === $session->get('playerId')) {
	$notification->setReaded(1);
	$this->getContainer()->get('entity_manager')->flush($notification);
} else {
	throw new FormException('Cette notification ne vous appartient pas');
}