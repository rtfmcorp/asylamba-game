<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;

# string description 	description à envoyer

$description 			= Utils::getHTTPData('description');

if ($description !== FALSE) {
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession(FALSE);
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	if (ASM::$pam->size() == 1) {
		if (ASM::$pam->get()->status > PAM_PARLIAMENT) {
			if ($description !== '' && strlen($description) < 25000) {
				$S_COL_1 = ASM::$clm->getCurrentSession();
				ASM::$clm->newSession();
				ASM::$clm->load(array('id' => ASM::$pam->get()->rColor));

				ASM::$clm->get()->description = $description;				
				
				ASM::$clm->changeSession($S_COL_1);
			} else {
				CTR::$alert->add('Le description est vide ou trop longue', ALERT_STD_FILLFORM);
			}
		} else {
			CTR::$alert->add('Vous n\'avez pas les droits pour poster une description', ALERT_STD_FILLFORM);
		}
	} else {
		CTR::$alert->add('Vous n\'existez pas', ALERT_STD_FILLFORM);
	}

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('Pas assez d\'informations pour écrire une description', ALERT_STD_FILLFORM);
}