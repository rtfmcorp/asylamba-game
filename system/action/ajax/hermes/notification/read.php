<?php
# read notification action

# int notif 		notif id


use Asylamba\Classes\Exception\FormException;

$id = $this->getContainer()->get('app.request')->query->get('notif');

if ($id === null) {
	throw new FormException('Erreur dans la requête AJAX');
}
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);

if (($notification = $notificationManager->get($id)) !== null && $notification->rPlayer === $session->get('playerId')) {
	$notification->setReaded(1);
	$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($notification);
} else {
	throw new FormException('Cette notification ne vous appartient pas');
}
