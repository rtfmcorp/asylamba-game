<?php
include_once ATHENA;
include_once GAIA;
include_once ARES;

# affect a commander

# int id 	 		id du commandant

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

		# checker si on a assez de place !!!!!
		$S_COM2 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('c.rBase' => $commander->rBase, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 2));
		$nbrLine2 = ASM::$com->size();

		ASM::$com->newSession();
		ASM::$com->load(array('c.rBase' => $commander->rBase, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 1));
		$nbrLine1 = ASM::$com->size();

		if ($commander->statement == Commander::INSCHOOL) {
			if ($nbrLine2 < PlaceResource::get(ASM::$obm->get()->typeOfBase, 'r-line')) {
				$commander->dAffectation = Utils::now();
				$commander->statement = Commander::AFFECTED;
				$commander->line = 2;

				# tutorial
				if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
					switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
						case TutorialResource::AFFECT_COMMANDER:
							TutorialHelper::setStepDone();
							break;
					}
				}

				CTR::$alert->add('Votre commandant ' . $commander->getName() . ' a bien été affecté en force de réserve', ALERT_STD_SUCCESS);
				CTR::redirect('fleet/commander-' . $commander->id . '/sftr-2');
			} elseif ($nbrLine1 < PlaceResource::get(ASM::$obm->get()->typeOfBase, 'l-line')) {
				$commander->dAffectation =Utils::now();
				$commander->statement = Commander::AFFECTED;
				$commander->line = 1;

				CTR::$alert->add('Votre commandant ' . $commander->getName() . ' a bien été affecté en force active', ALERT_STD_SUCCESS);
				CTR::redirect('fleet/commander-' . $commander->id . '/sftr-2');
			} else {
				CTR::$alert->add('Votre base a dépassé la capacité limite de commandants en activité', ALERT_STD_ERROR);			
			}
		} elseif ($commander->statement == Commander::AFFECTED) {
			$commander->statement = Commander::INSCHOOL;
			CTR::$alert->add('Votre commandant ' . $commander->getName() . ' a bien été remis à l\'école', ALERT_STD_SUCCESS);
			$commander->emptySquadrons();
			CTR::redirect('fleet');
		} else {
			CTR::$alert->add('Le status de votre commandant ne peut pas être modifié bien été remis à l\'école', ALERT_STD_ERROR);
		}

		ASM::$com->changeSession($S_COM2);
		ASM::$obm->changeSession($S_OBM);
	} else {
		CTR::$alert->add('Ce commandant n\'existe pas ou ne vous appartient pas', ALERT_STD_ERROR);
	}

	ASM::$com->changeSession($S_COM1);
} else {
	CTR::$alert->add('erreur dans le traitement de la requête', ALERT_BUG_ERROR);
}

?>