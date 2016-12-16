<?php
# met à jour tout ce qui concerne un joueur

use Asylamba\Classes\Library\Utils;

$session = $this->getContainer()->get('app.session');
$playerManager = $this->getContainer()->get('zeus.player_manager');

# mise à jour chaque heure
if (Utils::interval($session->get('lastUpdate')->get('game'), Utils::now(), 'h') > 0) {
	# update de l'heure dans le contrôleur
	$session->get('lastUpdate')->add('game', Utils::now());

	# mise à jour de tout
	$S_PAM1 = $playerManager->getCurrentSession();
	$playerManager->newSession(ASM_UMODE);
	$playerManager->load(array('id' => $session->get('playerId')));
	# --> uActionPoint et uCredit
		# --> instancie toutes les bases orbitales
			# --> uBuildingQueue
			# --> uShipQueue1
			# --> uShipQueue2
			# --> uTechnologyQueue
			# --> uResources
		# --> instancie tous les commandants
			# --> uTravel
			# --> uUxperienceInSchool
		# --> instancie les recherches et fait l'update
	$playerManager->changeSession($S_PAM1);
}

# mise à jour des sessions de temps en temps
/*if (Utils::interval($session->get('lastUpdate')->get('game'), Utils::now(), 's') > 3) {
	# update de l'heure dans le contrôleur
	$session->get('lastUpdate')->add('game', Utils::now());

	# on récupère l'id
	$id = $session->get('playerId');

	# on vide les sessions
	$session->clear();

	# on charge le joueur
	$S_PAM_UG = $playerManager->getCurrentSession();
	$playerManager->newSession();
	$playerManager->load(array('id' => $id));

	$player = $playerManager->get();
	var_dump($player);
	exit();
	include_once CONNECTION . 'create-session.php';

	$playerManager->changeSession($S_PAM_UG);

	# on log pour faire joli
	$path = 'public/log/stats/tmp.log';
	Bug::writeLog($path, "### recharge à : " . date('H:i:s') . " ###");
}*/
