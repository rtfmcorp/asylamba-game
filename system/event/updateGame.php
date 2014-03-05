<?php
# met à jour tout ce qui concerne un joueur
include_once ZEUS;

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
?>