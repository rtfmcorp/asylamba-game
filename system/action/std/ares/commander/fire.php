<?php

# fire a commander

# int id 	 		id du commandant

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;

$commanderId = Utils::getHTTPData('id');


if ($commanderId !== FALSE) {
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('c.id' => $commanderId, 'c.rPlayer' => CTR::$data->get('playerId')));

	if (ASM::$com->size() == 1) {
		$commander = ASM::$com->get();

		if ($commander->statement == 1) {
		
			// vider le commandant
			$commander->emptySquadrons();
			$commander->setStatement(4);

			CTR::$alert->add('Vous avez renvoyé votre commandant ' . $commander->getName() . '.', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('Vous ne pouvez pas renvoyer un officier en déplacement.', ALERT_STD_SUCCESS);
		}
	} else {
		CTR::$alert->add('Ce commandant n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
	}

	ASM::$com->changeSession($S_COM1);
} else {
	CTR::$alert->add('manque d\'information pour le traitement de la requête', ALERT_BUG_ERROR);
}