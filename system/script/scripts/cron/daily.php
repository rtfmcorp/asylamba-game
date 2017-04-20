<?php
# daily cron
# call at 4 am. every day

# tasks list
	# clean up notifications
	# check unactive players
	# ...

# worker

use Asylamba\Classes\Library\Bug;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Benchmark;
use Asylamba\Classes\Worker\API;
use Asylamba\Modules\Zeus\Model\Player;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$readNotificationTimeout = $this->getContainer()->getParameter('hermes.notifications.timeout.read');
$unreadNotificationTimeout = $this->getContainer()->getParameter('hermes.notifications.timeout.unread');
$playerGlobalInactiveTime = $this->getContainer()->getParameter('zeus.player.global_inactive_time');
$playerInactiveTimeLimit = $this->getContainer()->getParameter('zeus.player.inactive_time_limit');
$entityManager = $this->getContainer()->get('entity_manager');

$path = $this->getContainer()->getParameter('root_path') . '/public/log/cron/' . date('Y') . '-' . date('m') . '.log';

Bug::writeLog($path, '# ###################');
Bug::writeLog($path, '# Cron trace');
Bug::writeLog($path, '# ' . Utils::now());
Bug::writeLog($path, '# ###################');
Bug::writeLog($path, '');

# delete readed notifs older than 3 days
Bug::writeLog($path, '# Clean up redead notifications');
$bench = new Benchmark();

$readedNotifications = $notificationManager->getAllByReadState(1);

$deletedReadedNotifs = 0;
foreach ($readedNotifications as $notification) { 
	if (Utils::interval(Utils::now(), $notification->getDSending()) >= $readNotificationTimeout) {
		$entityManager->remove($notification);
		$deletedReadedNotifs++;
	}
}

Bug::writeLog($path, '# [OK] Status');
Bug::writeLog($path, '# [' . $bench->getTime('s', 3) . '] Execution time');
Bug::writeLog($path, '# [' . $deletedReadedNotifs . '] Deleted notifications');
Bug::writeLog($path, '');
$bench->clear();

# delete unreaded notifs older than 10 days
Bug::writeLog($path, '# Clean up unreaded notifications');
$bench->start();

$unreadedNotifications = $notificationManager->getAllByReadState(0);
$deletedUnreadedNotifs = 0;
foreach ($unreadedNotifications as $notification) { 
	if (Utils::interval(Utils::now(), $notification->getDSending()) >= $unreadNotificationTimeout) {
		$entityManager->remove($notification);
		$deletedUnreadedNotifs++;
	}
}

Bug::writeLog($path, '# [OK] Status');
Bug::writeLog($path, '# [' . $bench->getTime('s', 3) . '] Execution time');
Bug::writeLog($path, '# [' . $deletedUnreadedNotifs . '] Deleted notifications');
Bug::writeLog($path, '');
$bench->clear();

# check unactive players
Bug::writeLog($path, '# Check unactive players');
$bench->start();

$players = $playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE]);
$nbPlayers = count($players);
$unactivatedPlayers = 0;
$deletedPlayers 	= 0;
// @TODO understand this strange loop condition
for ($i = $nbPlayers - 1; $i >= 0; $i--) {
	$player = $players[$i];
	if (Utils::interval(Utils::now(), $player->getDLastConnection()) >= $playerInactiveTimeLimit) {

		$playerManager->kill($player->id);

		$deletedPlayers++;
	} elseif (Utils::interval(Utils::now(), $player->getDLastConnection()) >= $playerGlobalInactiveTime AND $player->statement == Player::ACTIVE) {
		$player->statement = Player::INACTIVE;

		if (APIMODE) {
			# sending email API call
			$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
			$api->sendMail($player->bind, APP_ID, API::TEMPLATE_INACTIVE_PLAYER);
		}

		$unactivatedPlayers++;
	}

}

# applique en cascade le changement de couleur des sytèmes
$this->getContainer()->get('gaia.galaxy_color_manager')->apply();

Bug::writeLog($path, '# [OK] Status');
Bug::writeLog($path, '# [' . $bench->getTime('s', 3) . '] Execution time');
Bug::writeLog($path, '# [' . $unactivatedPlayers . '] Players unactivated');
Bug::writeLog($path, '# [' . $deletedPlayers . '] Players deleted');
Bug::writeLog($path, '');
$bench->clear();

$entityManager->flush();

Bug::writeLog($path, '');

echo 'Done';