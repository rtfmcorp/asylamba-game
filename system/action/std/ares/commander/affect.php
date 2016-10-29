<?php

# affect a commander

# int id 	 		id du officier

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Zeus\Resource\TutorialResource;

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

		if ($commander->statement == Commander::INSCHOOL || $commander->statement == Commander::RESERVE) {
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

				CTR::$alert->add('Votre officier ' . $commander->getName() . ' a bien été affecté en force de réserve', ALERT_STD_SUCCESS);
				CTR::redirect('fleet/commander-' . $commander->id . '/sftr-2');
			} elseif ($nbrLine1 < PlaceResource::get(ASM::$obm->get()->typeOfBase, 'l-line')) {
				$commander->dAffectation =Utils::now();
				$commander->statement = Commander::AFFECTED;
				$commander->line = 1;

				# tutorial
				if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
					switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
						case TutorialResource::AFFECT_COMMANDER:
							TutorialHelper::setStepDone();
							break;
					}
				}

				CTR::$alert->add('Votre officier ' . $commander->getName() . ' a bien été affecté en force active', ALERT_STD_SUCCESS);
				CTR::redirect('fleet/commander-' . $commander->id . '/sftr-2');
			} else {
				CTR::$alert->add('Votre base a dépassé la capacité limite de officiers en activité', ALERT_STD_ERROR);			
			}
		} elseif ($commander->statement == Commander::AFFECTED) {
			$S_COM3 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array('c.rBase' => $commander->rBase, 'c.statement' => Commander::INSCHOOL));

			$commander->uCommander = Utils::now();
			if (ASM::$com->size() < PlaceResource::get(ASM::$obm->get()->typeOfBase, 'school-size')) {
				$commander->statement = Commander::INSCHOOL;
				CTR::$alert->add('Votre officier ' . $commander->getName() . ' a été remis à l\'école', ALERT_STD_SUCCESS);
				$commander->emptySquadrons();
			} else {
				$commander->statement = Commander::RESERVE;
				CTR::$alert->add('Votre officier ' . $commander->getName() . ' a été remis dans la réserve de l\'armée', ALERT_STD_SUCCESS);
				$commander->emptySquadrons();
			}
			ASM::$com->changeSession($S_COM3);
			CTR::redirect('fleet');
		} else {
			CTR::$alert->add('Le status de votre officier ne peut pas être modifié', ALERT_STD_ERROR);
		}

		ASM::$com->changeSession($S_COM2);
		ASM::$obm->changeSession($S_OBM);
	} else {
		CTR::$alert->add('Ce officier n\'existe pas ou ne vous appartient pas', ALERT_STD_ERROR);
	}

	ASM::$com->changeSession($S_COM1);
} else {
	CTR::$alert->add('erreur dans le traitement de la requête', ALERT_BUG_ERROR);
}