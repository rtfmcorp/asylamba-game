<?php

# change of line a commander

# int id 	 		id du commandant

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Resource\PlaceResource;

$commanderId = Utils::getHTTPData('id');

if ($commanderId !== FALSE) {
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('c.id' => $commanderId, 'c.rPlayer' => CTR::$data->get('playerId')));

	if (ASM::$com->size() == 1) {
		$commander = ASM::$com->get();
		
		$S_OBM = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $commander->rBase));

		if ($commander->statement == Commander::RESERVE) {
			$S_COM2 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array('c.rBase' => $commander->rBase, 'c.statement' => Commander::INSCHOOL));

			if (ASM::$com->size() < PlaceResource::get(ASM::$obm->get()->typeOfBase, 'school-size')) {
				$commander->statement = Commander::INSCHOOL;
				$commander->uCommander = Utils::now();
			} else {
				CTR::$alert->add('Votre école est déjà pleine.', ALERT_STD_ERROR);
			}

			ASM::$com->changeSession($S_COM2);
		} elseif ($commander->statement == Commander::INSCHOOL) {
			$commander->statement = Commander::RESERVE;
			$commander->uCommander = Utils::now();
		} else {
			CTR::$alert->add('Vous ne pouvez rien faire avec cet officier.', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM);
	} else {
		CTR::$alert->add('Ce commandant n\'existe pas ou ne vous appartient pas', ALERT_STD_ERROR);
	}

	ASM::$com->changeSession($S_COM1);
} else {
	CTR::$alert->add('erreur dans le traitement de la requête', ALERT_BUG_ERROR);
}