<?php
# read notification action

# int notif 		notif id


use App\Classes\Exception\FormException;

$id = $this->getContainer()->get('app.request')->query->get('notif');

if ($id === null) {
	throw new FormException('Erreur dans la requête AJAX');
}
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$notificationManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\NotificationManager::class);

if (($notification = $notificationManager->get($id)) !== null && $notification->rPlayer === $session->get('playerId')) {
	$notification->setReaded(1);
	$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush($notification);
} else {
	throw new FormException('Cette notification ne vous appartient pas');
}
