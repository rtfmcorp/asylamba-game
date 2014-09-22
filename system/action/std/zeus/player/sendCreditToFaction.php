<?php
include_once ZEUS;
# give credit action

# int quantity 		quantity of credit to send

$quantity = Utils::getHTTPData('quantity');

if ($quantity !== FALSE) {
	$credit = intval($quantity);

	if ($credit > 0) {

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(ASM_UMODE);
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

		if (ASM::$pam->size() == 1) {
			$sender = ASM::$pam->get();
			if ($sender->rColor > 0) {
				if ($sender->credit >= $credit) {
						
					$S_CLM1 = ASM::$clm->getCurrentSession();
					ASM::$clm->newSession();
					ASM::$clm->load(array('id' => $sender->rColor));	
					
					if (ASM::$clm->size() == 1) {
						# make the transaction
						$sender->decreaseCredit($credit);
						ASM::$clm->get()->increaseCredit($credit);

						CTR::$alert->add('Crédits envoyés', ALERT_STD_SUCCESS);
						ASM::$clm->changeSession($S_CLM1);

					} else {
						CTR::$alert->add('envoi de crédits impossible - faction introuvable', ALERT_STD_ERROR);
					}	
				} else {
					CTR::$alert->add('envoi de crédits impossible - vous ne pouvez pas envoyer plus que ce que vous possédez', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('envoi de crédits impossible - vous n\'avez pas de faction', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('envoi de crédits impossible - erreur dans le joueur', ALERT_STD_ERROR);
		}
		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::$alert->add('envoi de crédits impossible - il faut envoyer un nombre positif', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour envoyer des crédits', ALERT_STD_FILLFORM);
}
