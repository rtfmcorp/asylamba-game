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
		if (ASM::$clm->get($i)->isWinner == Color::WIN_CONFIRM) {
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
		$isTargetsValid = FALSE;

		for ($i = 1; $i <= VictoryResources::size(); $i++) { 
			$targets = VictoryResources::getInfo($i, 'targets');
			$isTargetValid = TRUE;

			foreach ($targets as $key => $target) {
				$sectors = 0;

				for ($j = 0; $j < $sm->size(); $j++) {
					if ($sm->get($j)->rColor == $faction->id && in_array($sm->get($j)->id, $target['sectors'])) {
						$sectors++;
					}
				}

				$isTargetValid = $sectors >= $target['nb']
					? $isTargetValid && TRUE
					: $isTargetValid && FALSE;
			}

			$isTargetsValid = $isTargetsValid || $isTargetValid;
		}

		if ($isTargetsValid) {
			# la faction gagne
			$faction->isWinner = Color::WIN_TARGET;
			$faction->dClaimVictory = Utils::now();

			# envoi de notif aux chefs de factions
			$S_PAM = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession();
			ASM::$pam->load(['status' => PAM_CHIEF]);

			for ($i = 0; $i < ASM::$pam->size(); $i++) { 
				$player = ASM::$pam->get($i);

				if ($player->id !== CTR::$data->get('playerId')) {
					# notif : tenir pendant un moment
					$notif = new Notification();
					$notif->setRPlayer($player->id);
					$notif->setTitle(ColorResource::getInfo($faction->id, 'popularName') . ' revendique la victoire');
					$notif->dSending = Utils::now();
					$notif->addBeg()
						->addLnk(APP_ROOT . 'embassy/faction-' . $faction->id, ColorResource::getInfo($faction->id, 'popularName'))
						->addTxt(' revendique la victoire. Vous avez ' . HOURS_TO_WIN . ' relèves pour l\'en empêcher. Prenez rapidement certains de ses secteurs.')
						->addEnd();
					ASM::$ntm->add($notif);
				}
			}

			ASM::$pam->changeSession($S_PAM);
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