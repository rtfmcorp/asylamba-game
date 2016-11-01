<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;

# change of line a commander

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
		if ($commander->line == 1) {
			$S_COM2 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array('c.rBase' => $commander->rBase, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 2));
			$nbrLine2 = ASM::$com->size();

			if ($nbrLine2 < PlaceResource::get(ASM::$obm->get()->typeOfBase, 'r-line')) {
				$commander->line = 2;

				CTR::redirect();
				
			} else {
				$commander->line = 2;
				ASM::$com->get()->line = 1;
				CTR::redirect();
				CTR::$alert->add('Votre commandant ' . $commander->getName() . ' a échangé sa place avec ' . ASM::$com->get()->name . '.', ALERT_STD_SUCCESS);
			}
			ASM::$com->changeSession($S_COM2);
		} else {
			$S_COM2 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array('c.rBase' => $commander->rBase, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 1));
			$nbrLine1 = ASM::$com->size();

			# tutorial
			if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
				switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
					case TutorialResource::MOVE_FLEET_LINE:
						TutorialHelper::setStepDone();
						break;
				}
			}
			
			if ($nbrLine1 < PlaceResource::get(ASM::$obm->get()->typeOfBase, 'l-line')) {
				$commander->line = 1;

				CTR::redirect();
			} else {
				$commander->line = 1;
				ASM::$com->get()->line = 2;
				CTR::redirect();
				CTR::$alert->add('Votre commandant ' . $commander->getName() . ' a échangé sa place avec ' . ASM::$com->get()->name . '.', ALERT_STD_SUCCESS);
			}
			ASM::$com->changeSession($S_COM2);
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