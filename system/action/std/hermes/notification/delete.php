<?php
# archive or unarchive action

# int id 			id de la notif

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;

$id = $this->getContainer()->get('app.request')->query->get('id');

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');

if ($id) {
	$S_NTM1 = $notificationManager->getCurrentSession();
	$notificationManager->newSession(ASM_UMODE);
	$notificationManager->load(array('id' => $id));
	if ($notificationManager->size() == 1 && $notificationManager->get()->rPlayer == $session->get('playerId')) {
		$notificationManager->deleteById($id);	
		$response->flashbag->add('Notification supprimée', Response::FLASHBAG_SUCCESS);
	} else {
		throw new ErrorException('C\'est pas très bien de supprimer les notifications des autres.');
	}
	$notificationManager->changeSession($S_NTM1);
} else {
	throw new ErrorException('Cette notification n\'existe pas');
}