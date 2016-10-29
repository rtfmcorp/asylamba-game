<?php
# met à jour tout ce qui concerne un joueur

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;

# mise à jour chaque heure
if (Utils::interval(CTR::$data->get('lastUpdate')->get('game'), Utils::now(), 'h') > 0) {
	# update de l'heure dans le contrôleur
	CTR::$data->get('lastUpdate')->add('game', Utils::now());

	# mise à jour de tout
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession(ASM_UMODE);
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
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
	ASM::$pam->changeSession($S_PAM1);
}

# mise à jour des sessions de temps en temps
/*if (Utils::interval(CTR::$data->get('lastUpdate')->get('game'), Utils::now(), 's') > 3) {
	# update de l'heure dans le contrôleur
	CTR::$data->get('lastUpdate')->add('game', Utils::now());

	# on récupère l'id
	$id = CTR::$data->get('playerId');

	# on vide les sessions
	CTR::$data->clear();

	# on charge le joueur
	$S_PAM_UG = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => $id));

	$player = ASM::$pam->get();
	var_dump($player);
	exit();
	include_once CONNECTION . 'create-session.php';

	ASM::$pam->changeSession($S_PAM_UG);

	# on log pour faire joli
	$path = 'public/log/stats/tmp.log';
	Bug::writeLog($path, "### recharge à : " . date('H:i:s') . " ###");
}*/
