<?php
# delete all notifications

use App\Classes\Library\Flashbag;

$notificationManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\NotificationManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);

$nbr = $notificationManager->deleteByRPlayer($session->get('playerId'));

if ($nbr > 1) {
	$session->addFlashbag($nbr . ' notifications ont été supprimées.', Flashbag::TYPE_SUCCESS);
} else if ($nbr == 1) {
	$session->addFlashbag('Une notification a été supprimée.', Flashbag::TYPE_SUCCESS);
} else {
	$session->addFlashbag('Toutes vos notifications ont déjà été supprimées.', Flashbag::TYPE_SUCCESS);
}
