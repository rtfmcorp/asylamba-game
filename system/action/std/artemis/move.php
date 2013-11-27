<?php
# send a fleet to move to a place

# int spyId 			id de l'espion à envoyer
# int systemId				id du systeme de destination

include_once GAIA;
include_once ARTEMIS;
include_once ZEUS;

if (CTR::$get->exist('spyid')) {
	$spyId = CTR::$get->get('spyid');
} else if (CTR::$post->exist('spyid')) {
	$spyId = CTR::$post->get('spyid');
} else {
	$spyId = FALSE;
}

if (CTR::$get->exist('systemid')) {
	$systemId = CTR::$get->get('systemid');
} else if (CTR::$post->exist('systemid')) {
	$systemId = CTR::$post->get('systemid');
} else {
	$systemId = FALSE;
}

if ($spyId AND $systemId) {
	$S_SPY1 = ASM::$spy->getCurrentSession();
	ASM::$spy->newSession(ASM_UMODE);
	ASM::$spy->load(array('id' => $spyId, 'rPlayer' => CTR::$data->get('playerId')));
	$spy = ASM::$spy->get();

	$S_SYS1 = ASM::$sys->getCurrentSession();
	ASM::$sys->newSession(ASM_UMODE);
	ASM::$sys->load(array('id' => $systemId));
	$system = ASM::$sys->get();

	if (ASM::$spy->size() > 0) {
		if (ASM::$sys->size() > 0) {
				if (CTR::$data->get('playerInfo')->get('credit') >= SPY_CREDITSTOMOVE) {
				ASM::$sys->load(array('id' => $spy->rSystem));
				$currentSystem = ASM::$sys->getById($spy->rSystem);
				$duration = GAME::getTimeToTravelToSystem($currentSystem, $system);
				if ($spy->move($system->id, $duration)){
					$S_PAM1 = ASM::$pam->getCurrentSession();
					ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
					$player = ASM::$pam->get();
					$player->decreaseCredit(SPY_CREDITSTOMOVE);
					ASM::$pam->changeSession($S_PAM1);
					CTR::$alert->add('Espion envoyé.', ALERT_STD_SUCCESS);
				}
			} else {
				CTR::$alert->add('Vous n\'avez pas assez de crédits.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Ce system n\'existe pas.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Cet espion n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
	}
	ASM::$spy->changeSession($S_SPY1);
	ASM::$sys->changeSession($S_SYS1);
} else {
	CTR::$alert->add('Manque de précision sur l\'espion ou la position.', ALERT_STD_ERROR);
}