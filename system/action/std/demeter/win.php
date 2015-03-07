<?php
include_once ZEUS;
include_once GAIA;
include_once DEMETER;

# seul le chef peux le faire
if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
	# check : aucune faction n'a encore revendiqué la victoire
	$S_CLM_1 = ASM::$clm->getCurrentSession();
	ASM::$clm->newSession(FALSE);
	ASM::$clm->load();

	$hasAlreadyWin 	= FALSE;
	$faction 		= NULL;

	for ($i = 0; $i < ASM::$clm->size(); $i++) { 
		if (ASM::$clm->get($i)->isWinner) {
			$hasAlreadyWin = TRUE;
		}

		if (ASM::$clm->get($i)->id == CTR::$data->get('playerInfo')->get('color')) {
			$faction = ASM::$clm->get($i);
		}
	}

	if (!$hasAlreadyWin) {
		# check : les objectifs sont atteint

		# chargement des secteurs
		$sm = new SectorManager();
		$sm->load();

		# vérification des objectifs
		for ($i = 1; $i <= VictoryResources::size(); $i++) { 
			$targets = VictoryResources::getInfo($i ,'targets');
			$isValid = 0;

			foreach ($targets as $key => $target) {
				$sectors = 0;

				for ($i = 0; $i < $sm->size(); $i++) {
					if ($sm->get($i)->rColor == $faction->id && in_array($sm->get($i)->id, $target['sectors'])) {
						$sectors++;
					}
				}

				if ($sectors >= $target['nb']) {
					$isValid++;
				}
			}
		}

		if ($isValid == count($targets)) {
			# la faction gagne
			$faction->isWinner == TRUE;

			# envoi de notif aux chefs de factions
			# TODO

			# page de "garde" pour tout les joueurs
			# TODO

			CTR::$alert->add('Vous avez gagné !', ALERT_STD_ERROR);
		} else {
			CTR::$alert->add('Tous les objectifs ne sont pas rempli.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('La victoire a déjà été revendiquée.', ALERT_STD_ERROR);
	}

	ASM::$clm->changeSession($S_CLM_1);
} else {
	CTR::$alert->add('Seul le chef de la faction peut revendiquer la victoire.', ALERT_STD_ERROR);
}