<?php
# archive or unarchive action

# int id 			id de la notif

use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->query->get('id');

$notificationManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\NotificationManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$entityManager = $this->getContainer()->get(\App\Classes\Entity\EntityManager::class);

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
