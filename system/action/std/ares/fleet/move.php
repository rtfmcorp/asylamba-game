<?php
include_once ARES;
include_once GAIA;
include_once ZEUS;
# send a fleet to move to a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place de destination

if (CTR::$get->exist('commanderid')) {
	$commanderId = CTR::$get->get('commanderid');
} else if (CTR::$post->exist('commanderid')) {
	$commanderId = CTR::$post->get('commanderid');
} else {
	$commanderId = FALSE;
}

if (CTR::$get->exist('placeid')) {
	$placeId = CTR::$get->get('placeid');
} else if (CTR::$post->exist('placeid')) {
	$placeId = CTR::$post->get('placeid');
} else {
	$placeId = FALSE;
}

if ($commanderId !== FALSE AND $placeId !== FALSE) {
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession(ASM_UMODE);
	ASM::$com->load(array('id' => $commanderId, 'rPlayer' => CTR::$data->get('playerId')));
	$commander = ASM::$com->get();
	
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession(ASM_UMODE);
	ASM::$plm->load(array('id' => $placeId));
	$place = ASM::$plm->get();

	if (count($place->commanders) < 3) {
		if (ASM::$com->size() > 0) {
			if (ASM::$plm->size() > 0) {
				if (CTR::$data->get('playerId') == $place->getRPlayer()) {
					ASM::$plm->load(array('id' => $commander->getRBase()));
					$home = ASM::$plm->getById($commander->getRBase());

					$duration = Game::getTimeToTravel($home, $place);
					$PAToTravel = Game::getPAToTravel($duration);

					if (CTR::$data->get('playerInfo')->get('actionPoint') >= $PAToTravel) {
						if ($commander->move($place->getId(), COM_MOVE, $duration)) {
							$S_PAM1 = ASM::$pam->getCurrentSession();
							ASM::$pam->newSession(ASM_UMODE);
							ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
							$player = ASM::$pam->get();
							$player->setActionPoint($player->getActionPoint() - $PAToTravel);
							ASM::$pam->changeSession($S_PAM1);
							CTR::$alert->add('Flotte envoyée.', ALERT_STD_SUCCESS);
						}
					} else {
						CTR::$alert->add('Vous n\'avez pas assez de points d\'attaque.', ALERT_STD_ERROR);
					}			
				} else {
					CTR::$alert->add('Vous ne pouvez pas envoyer une flotte sur une planète qui ne vous appartient pas.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Ce lieu n\'existe pas.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Ce commandant ne vous appartient pas ou n\'existe pas.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('La destination n\'a pas d\'emplacement de flotte libre.', ALERT_STD_ERROR);
	}
	ASM::$com->changeSession($S_COM1);
	ASM::$plm->changeSession($S_PLM1);
} else {
	CTR::$alert->add('Manque de précision sur le commandant ou la position.', ALERT_STD_ERROR);
}