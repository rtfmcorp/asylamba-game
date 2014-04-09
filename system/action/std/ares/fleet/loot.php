<?php
include_once ARES;
include_once GAIA;
include_once ZEUS;
# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

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
	ASM::$com->load(array('c.id' => $commanderId, 'c.rPlayer' => CTR::$data->get('playerId')));
	
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession(ASM_UMODE);
	ASM::$plm->load(array('id' => $placeId));

	if (ASM::$com->size() > 0) {
		if (ASM::$plm->size() > 0) {
			$commander = ASM::$com->get();
			$place = ASM::$plm->get();

			if (CTR::$data->get('playerInfo')->get('color') != $place->getPlayerColor()) {
				ASM::$plm->load(array('id' => $commander->getRBase()));
				$home = ASM::$plm->getById($commander->getRBase());

				$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
				$duration = Game::getTimeToTravel($home, $place);

				if ($commander->move($place->getId(), $commander->rBase, Commander::LOOT, $length, $duration)) {
					$commander->dStart = lib::now();
					CTR::$alert->add('Flotte envoyée.', ALERT_STD_SUCCESS);

					if (CTR::$get->exist('redirect')) {
						CTR::redirect('map/place-' . CTR::$get->get('redirect'));
					}
				}		
			} else {
				CTR::$alert->add('Vous ne pouvez pas attaquer un lieu appartenant à votre Faction.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Ce lieu n\'existe pas.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Ce commandant ne vous appartient pas ou n\'existe pas.', ALERT_STD_ERROR);
	}
	ASM::$com->changeSession($S_COM1);
	ASM::$plm->changeSession($S_PLM1);
} else {
	CTR::$alert->add('Manque de précision sur le commandant ou la position.', ALERT_STD_ERROR);
}