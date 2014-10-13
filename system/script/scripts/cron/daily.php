<?php
# daily cron
# call at 4 am. every day

# tasks list
	# clean up notifications
	# check unactive players
	# ...

# worker
include_once HERMES;
$S_NTM1 = ASM::$ntm->getCurrentSession();
include_once ZEUS;
$S_PAM1 = ASM::$pam->getCurrentSession();

$path = 'public/log/cron/' . date('Y') . '-' . date('m') . '.log';

Bug::writeLog($path, '# ###################');
Bug::writeLog($path, '# Cron trace');
Bug::writeLog($path, '# ' . Utils::now());
Bug::writeLog($path, '# ###################');
Bug::writeLog($path, '');

# delete readed notifs older than 3 days
Bug::writeLog($path, '# Clean up redead notifications');
$bench = new Benchmark();

ASM::$ntm->newSession();
ASM::$ntm->load(array('readed' => 1, 'archived' => 0));

$deletedReadedNotifs = 0;
for ($i = ASM::$ntm->size() - 1; $i >= 0; $i--) { 
	if (Utils::interval(Utils::now(), ASM::$ntm->get($i)->getDSending()) >= NTM_TIMEOUT_READED) {
		ASM::$ntm->deleteById(ASM::$ntm->get($i)->getId());
		$deletedReadedNotifs++;
	}
}

Bug::writeLog($path, '# [OK] Status');
Bug::writeLog($path, '# [' . $bench->getTime('s', 3) . '] Execution time');
Bug::writeLog($path, '# [' . $deletedReadedNotifs . '] Deleted notifications');
Bug::writeLog($path, '');
$bench->clear();

# delete unreaded notifs older than 10 days
Bug::writeLog($path, '# Clean up unredead notifications');
$bench->start();

ASM::$ntm->newSession();
ASM::$ntm->load(array('readed' => 0, 'archived' => 0));

$deletedUnreadedNotifs = 0;
for ($i = ASM::$ntm->size() - 1; $i >= 0; $i--) { 
	if (Utils::interval(Utils::now(), ASM::$ntm->get($i)->getDSending()) >= NTM_TIMEOUT_UNREADED) {
		ASM::$ntm->deleteById(ASM::$ntm->get($i)->getId());
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

ASM::$pam->newSession(FALSE);
ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE)));

$unactivatedPlayers = 0;
$deletedPlayers 	= 0;
for ($i = ASM::$pam->size() - 1; $i >= 0; $i--) { 
	if (Utils::interval(Utils::now(), ASM::$pam->get($i)->getDLastConnection()) >= PAM_TIME_LIMIT_INACTIVE) {

		ASM::$pam->kill(ASM::$pam->get($i)->id);

		$deletedPlayers++;
	} elseif (Utils::interval(Utils::now(), ASM::$pam->get($i)->getDLastConnection()) >= PAM_TIME_GLOBAL_INACTIVE) {
		ASM::$pam->get($i)->statement = PAM_INACTIVE;

		# sending email API call
		$api = new API(GETOUT_ROOT);
		$api->sendMail(ASM::$pam->get($i)->bind, APP_ID, API::TEMPLATE_INACTIVE_PLAYER);

		$unactivatedPlayers++;
	}
}

# applique en cascade le changement de couleur des sytèmes
include_once GAIA;
GalaxyColorManager::apply();

Bug::writeLog($path, '# [OK] Status');
Bug::writeLog($path, '# [' . $bench->getTime('s', 3) . '] Execution time');
Bug::writeLog($path, '# [' . $unactivatedPlayers . '] Players unactivated');
Bug::writeLog($path, '# [' . $deletedPlayers . '] Players deleted');
Bug::writeLog($path, '');
$bench->clear();

# close object
ASM::$ntm->changeSession($S_NTM1);
ASM::$pam->changeSession($S_PAM1);

Bug::writeLog($path, '');
?>