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
	$notificationManager->deleteById($id);	
	$notificationManager->changeSession($S_NTM1);
} else {
	throw new ErrorException('Cette notification n\'existe pas');
}