<?php
# archive or unarchive action
# int id 			id de la notif

use App\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->query->get('id');

$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

if ($id) {
	if (($notification = $notificationManager->get($id)) !== null && $notification->rPlayer === $session->get('playerId')) {
		$notification->setArchived(!$notification->getArchived());
		$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($notification);
	} else {
		throw new ErrorException('Ce n\'est pas bien d\'archiver les notifications des autres.');
	}
} else {
	throw new ErrorException('cette notification n\'existe pas');
}
