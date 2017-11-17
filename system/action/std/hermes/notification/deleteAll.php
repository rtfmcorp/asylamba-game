<?php
# delete all notifications

use Asylamba\Classes\Library\Flashbag;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$session = $this->getContainer()->get('session_wrapper');

$nbr = $notificationManager->deleteByRPlayer($session->get('playerId'));

if ($nbr > 1) {
    $session->addFlashbag($nbr . ' notifications ont été supprimées.', Flashbag::TYPE_SUCCESS);
} elseif ($nbr == 1) {
    $session->addFlashbag('Une notification a été supprimée.', Flashbag::TYPE_SUCCESS);
} else {
    $session->addFlashbag('Toutes vos notifications ont déjà été supprimées.', Flashbag::TYPE_SUCCESS);
}
