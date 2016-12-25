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

$S_NTM1 = $notificationManager->getCurrentSession();
$S_PAM1 = $playerManager->getCurrentSession();

$path = 'public/log/cron/' . date('Y') . '-' . date('m') . '.log';

Bug::writeLog($path, '# ###################');
Bug::writeLog($path, '# Cron trace');
Bug::writeLog($path, '# ' . Utils::now());
Bug::writeLog($path, '# ###################');
Bug::writeLog($path, '');

# delete readed notifs older than 3 days
Bug::writeLog($path, '# Clean up redead notifications');
$bench = new Benchmark();

$notificationManager->newSession();
$notificationManager->load(array('readed' => 1, 'archived' => 0));

$deletedReadedNotifs = 0;
for ($i = $notificationManager->size() - 1; $i >= 0; $i--) { 
	if (Utils::interval(Utils::now(), $notificationManager->get($i)->getDSending()) >= $readNotificationTimeout) {
		$notificationManager->deleteById($notificationManager->get($i)->getId());
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

$notificationManager->newSession();
$notificationManager->load(array('readed' => 0, 'archived' => 0));

$deletedUnreadedNotifs = 0;
for ($i = $notificationManager->size() - 1; $i >= 0; $i--) { 
	if (Utils::interval(Utils::now(), $notificationManager->get($i)->getDSending()) >= $unreadNotificationTimeout) {
		$notificationManager->deleteById($notificationManager->get($i)->getId());
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

$playerManager->newSession(FALSE);
$playerManager->load(array('statement' => array(Player::ACTIVE, Player::INACTIVE)));

$unactivatedPlayers = 0;
$deletedPlayers 	= 0;
for ($i = $playerManager->size() - 1; $i >= 0; $i--) { 
	if (Utils::interval(Utils::now(), $playerManager->get($i)->getDLastConnection()) >= $playerInactiveTimeLimit) {

		$playerManager->kill($playerManager->get($i)->id);

		$deletedPlayers++;
	} elseif (Utils::interval(Utils::now(), $playerManager->get($i)->getDLastConnection()) >= $playerGlobalInactiveTime AND $playerManager->get($i)->statement == Player::ACTIVE) {
		$playerManager->get($i)->statement = Player::INACTIVE;

		# sending email API call
		$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
		$api->sendMail($playerManager->get($i)->bind, APP_ID, API::TEMPLATE_INACTIVE_PLAYER);

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

# close object
$notificationManager->changeSession($S_NTM1);
$playerManager->changeSession($S_PAM1);

Bug::writeLog($path, '');

echo 'Done';