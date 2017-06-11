<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\API;
use Asylamba\Modules\Zeus\Model\Player;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$readNotificationTimeout = $this->getContainer()->getParameter('hermes.notifications.timeout.read');
$unreadNotificationTimeout = $this->getContainer()->getParameter('hermes.notifications.timeout.unread');
$playerGlobalInactiveTime = $this->getContainer()->getParameter('zeus.player.global_inactive_time');
$playerInactiveTimeLimit = $this->getContainer()->getParameter('zeus.player.inactive_time_limit');
$entityManager = $this->getContainer()->get('entity_manager');

$path = $this->getContainer()->getParameter('log_directory') . '/cron/' . date('Y') . '-' . date('m') . '.log';

$readedNotifications = $notificationManager->getAllByReadState(1);

$deletedReadedNotifs = 0;
foreach ($readedNotifications as $notification) { 
	if (Utils::interval(Utils::now(), $notification->getDSending()) >= $readNotificationTimeout) {
		$entityManager->remove($notification);
		$deletedReadedNotifs++;
	}
}

$unreadedNotifications = $notificationManager->getAllByReadState(0);
$deletedUnreadedNotifs = 0;
foreach ($unreadedNotifications as $notification) { 
	if (Utils::interval(Utils::now(), $notification->getDSending()) >= $unreadNotificationTimeout) {
		$entityManager->remove($notification);
		$deletedUnreadedNotifs++;
	}
}

$players = $playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE]);
$nbPlayers = count($players);
// @TODO understand this strange loop condition
for ($i = $nbPlayers - 1; $i >= 0; $i--) {
	$player = $players[$i];
	if (Utils::interval(Utils::now(), $player->getDLastConnection()) >= $playerInactiveTimeLimit) {

		$playerManager->kill($player->id);
	} elseif (Utils::interval(Utils::now(), $player->getDLastConnection()) >= $playerGlobalInactiveTime AND $player->statement == Player::ACTIVE) {
		$player->statement = Player::INACTIVE;

		if ($this->getContainer()->getParameter('apimode') === 'enabled') {
			# sending email API call
			$this->getContainer()->get('api')->sendMail($player->bind, API::TEMPLATE_INACTIVE_PLAYER);
		}
	}

}

$entityManager->flush();

echo 'Done';