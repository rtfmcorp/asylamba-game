<?php
# archive or unarchive action
# int id 			id de la notif

use Asylamba\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->request->get('id');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

if ($id) {
	$S_NTM1 = $notificationManager->getCurrentSession();
	$notificationManager->newSession(ASM_UMODE);
	$notificationManager->load(array('id' => $id));
	$notif = $notificationManager->get();
	if ($notif->getArchived() == 0) {
		$notif->setArchived(1);
	} else {
		$notif->setArchived(0);
	}
	$notificationManager->changeSession($S_NTM1);
} else {
	throw new ErrorException('cette notification n\'existe pas');
}
