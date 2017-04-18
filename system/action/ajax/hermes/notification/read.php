<?php
# read notification action

# int notif 		notif id


use Asylamba\Classes\Exception\FormException;

$notif = $this->getContainer()->get('app.request')->query->get('notif');

if ($notif === null) {
	throw new FormException('Erreur dans la requête AJAX');
}
$session = $this->getContainer()->get('app.session');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$S_NTM1 = $notificationManager->getCurrentSession();
$notificationManager->newSession();
$notificationManager->load(array('id' => $notif, 'rPlayer' => $session->get('playerId')));

if ($notificationManager->size() == 1) {
	$notificationManager->get()->setReaded(1);
} else {
	throw new FormException('Cette notification ne vous appartient pas');
}

$notificationManager->changeSession($S_NTM1);