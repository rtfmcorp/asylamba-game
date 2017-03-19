<?php
# archive or unarchive action
# int id 			id de la notif

use Asylamba\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->query->get('id');

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$session = $this->getContainer()->get('app.session');

if ($id) {
	$S_NTM1 = $notificationManager->getCurrentSession();
	$notificationManager->newSession(ASM_UMODE);
	$notificationManager->load(array('id' => $id));
	if ($notificationManager->size() == 1 && $notificationManager->get()->rPlayer == $session->get('playerId')) {
		$notif = $notificationManager->get();
		if ($notif->getArchived() == 0) {
			$notif->setArchived(1);
		} else {
			$notif->setArchived(0);
		}
	} else {
		throw new ErrorException('Ce n\'est pas bien d\'archiver les notifications des autres.');
	}
	$notificationManager->changeSession($S_NTM1);
} else {
	throw new ErrorException('cette notification n\'existe pas');
}
