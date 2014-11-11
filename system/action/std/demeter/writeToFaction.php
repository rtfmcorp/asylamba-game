<?php
include_once HERMES;
include_once ZEUS;

# int player 		id du joueur (facultatif)
# string message 	message à envoyer

$message 			= Utils::getHTTPData('message');

$n3 				= Utils::getHTTPData('target-n3');
$n2 				= Utils::getHTTPData('target-n2');
$n1 				= Utils::getHTTPData('target-n1');

$sender 			= -1;

# protection des inputs
$p = new Parser();
$message = $p->protect($message);

if ($message !== FALSE) {
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession(FALSE);
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	if (ASM::$pam->size() == 1) {
		if (ASM::$pam->get()->status > PAM_PARLIAMENT) {
			if ($message !== '' && strlen($message) < 25000) {
				$counter = 0;
				$faction = ASM::$pam->get()->rColor;
				$avbRank = array();

				if ($n3 !== FALSE) {
					$avbRank[] = PAM_CHIEF;
					$avbRank[] = PAM_MINISTER;
					$avbRank[] = PAM_WARLORD;
					$avbRank[] = PAM_TREASURER;
				}

				if ($n2 !== FALSE) {
					$avbRank[] = PAM_PARLIAMENT;
				}

				if ($n1 !== FALSE) {
					$avbRank[] = PAM_STANDARD;
				}

				if (!empty($avbRank)) {
					ASM::$pam->newSession(FALSE);
					ASM::$pam->load(array('rColor' => $faction, 'status' => $avbRank));

					$S_MSM1 = ASM::$msm->getCurrentSession();

					for ($i = 0; $i < ASM::$pam->size(); $i++) {
						# message
						$m = new Message();
						$m->setRPlayerWriter($sender);
						$m->setDSending(Utils::now());
						$m->setContent($message);

						$playerId = ASM::$pam->get($i)->id;

						ASM::$msm->newSession();
						ASM::$msm->load(array('rPlayerReader' => $playerId, 'rPlayerWriter' => $sender));

						if (ASM::$msm->size() > 0) {
							# thread existant
							$m->setThread(ASM::$msm->get()->getThread());
							$m->setRPlayerReader($playerId);

							ASM::$msm->add($m);
						} else {
							# création d'un nouveau thread
							$db = DataBase::getInstance();
							$qr = $db->prepare('SELECT MAX(thread) AS maxThread FROM message');
							$qr->execute();

							if ($aw = $qr->fetch()) {
								$m->setThread($aw['maxThread'] + 1);
								$m->setRPlayerReader($playerId);

								ASM::$msm->add($m);
							}
						}
						
						$counter++;
					}

					CTR::$alert->add($counter . ' message' . Format::plural($counter) . ' envoyé' . Format::plural($counter), ALERT_STD_SUCCESS);

					ASM::$msm->changeSession($S_MSM1);
				} else {
					CTR::$alert->add('Vous n\'avez sélectionné aucun groupe de joueur', ALERT_STD_FILLFORM);
				}
			} else {
				CTR::$alert->add('Le message est vide ou trop long', ALERT_STD_FILLFORM);
			}
		} else {
			CTR::$alert->add('Ce joueur n\'a pas les droits pour poster un message officiel', ALERT_STD_FILLFORM);
		}
	} else {
		CTR::$alert->add('Ce joueur n\'existe pas', ALERT_STD_FILLFORM);
	}

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('Pas assez d\'informations pour écrire un message officiel', ALERT_STD_FILLFORM);
}