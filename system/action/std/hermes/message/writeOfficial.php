<?php
include_once HERMES;
include_once ZEUS;
# write official message action (from player 0 = Jean-Mi)

# int player 		id du joueur (facultatif)
# int ally 			id de l'alliance (facultatif)
# string message 	message à envoyer

if (CTR::$data->get('playerInfo')->get('admin') == FALSE) {
	CTR::redirect('profil');
} else {
	$playerId = Utils::getHTTPData('player');
	$ally = Utils::getHTTPData('ally');
	$message = Utils::getHTTPData('message');

	// protection des inputs
	$p = new Parser();
	$message = $p->protect($message);

	if ($message !== '') {
		# sender
		$jeanMi = 0;
		$messageCounter = 0;

		# message
		$m = new Message();
		$m->setRPlayerWriter($jeanMi);
		$m->setDSending(Utils::now());
		$m->setContent($message);

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);

		if ($playerId !== '') {
			# envoi à un seul joueur
			ASM::$pam->load(array('id' => $playerId));
		} else if (in_array($ally, array(1, 2, 3, 4, 5, 6, 7))) {
			# envoi aux membres d'une faction
			ASM::$pam->load(array('rColor' => $ally, 'statement' => PAM_ACTIVE));
		} else {
			# envoi à tous les joueurs
			ASM::$pam->load(array('statement' => PAM_ACTIVE));
		}
		
		$S_MSM1 = ASM::$msm->getCurrentSession();

		for ($i = 0; $i < ASM::$pam->size(); $i++) { 
			ASM::$msm->newSession();
			$player = ASM::$pam->get($i);

			ASM::$msm->load(array('rPlayerReader' => $player->getId(), 'rPlayerWriter' => $jeanMi));
			if (ASM::$msm->get()) {
				// thread existant
				$m->setThread(ASM::$msm->get()->getThread());
				$m->setRPlayerReader($player->getId());
				ASM::$msm->add($m);
				$messageCounter++;
			} else {
				// création d'un nouveau thread
				$db = DataBase::getInstance();
				$qr = $db->prepare('SELECT MAX(thread) AS maxThread FROM message');
				$qr->execute();
				if ($aw = $qr->fetch()) {
					$m->setThread($aw['maxThread'] + 1);
					$m->setRPlayerReader($player->getId());
					ASM::$msm->add($m);
					$messageCounter++;
				} else {
					CTR::$alert->add('création de message impossible pour le joueur d\'id ' . $player->getId(), ALERT_STD_ERROR);
				}
			}
		}
		if ($messageCounter <= 1) {
			CTR::$alert->add($messageCounter . ' message envoyé', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add($messageCounter . ' messages envoyés', ALERT_STD_SUCCESS);
		}
		ASM::$msm->changeSession($S_MSM1);
		ASM::$pam->changeSession($S_PAM1);

	} else {
		CTR::$alert->add('pas assez d\'informations pour écrire un message officiel', ALERT_STD_FILLFORM);
	}
}
?>