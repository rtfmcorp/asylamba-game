<?php
include_once ARES;
include_once GAIA;
include_once ZEUS;
include_once PROMETHEE;
# send a fleet to conquer a place

# int commanderId 			id du commandant à envoyer
# int placeId				id de la place attaquée

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
	
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession(ASM_UMODE);
	ASM::$plm->load(array('id' => $placeId));

	// load the technologies
	$technologies = new Technology(CTR::$data->get('playerId'));

	# check si technologie CONQUEST débloquée
	if ($technologies->getTechnology(Technology::CONQUEST) == 1) {
		# check si la technologie BASE_QUANTITY a un niveau assez élevé
		$maxBasesQuantity = $technologies->getTechnology(Technology::BASE_QUANTITY) + 1;
		$obQuantity = CTR::$data->get('playerBase')->get('ob')->size();
		$msQuantity = CTR::$data->get('playerBase')->get('ms')->size();
		$coloQuantity = 0;
		$S_COM2 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('rPlayer' => CTR::$data->get('playerId'), 'statement' => COM_MOVING));
		for ($i = 0; $i < ASM::$com->size(); $i++) { 
			if (ASM::$com->get($i)->getTypeOfMove() == COM_COLO) {
				$coloQuantity++;
			}
		}
		ASM::$com->changeSession($S_COM2);
		if ($obQuantity + $msQuantity + $coloQuantity < $maxBasesQuantity) {
			# check si le commandant existe
			if (ASM::$com->size() > 0) {
				# check si le lieu existe
				if (ASM::$plm->size() > 0) {
					$commander = ASM::$com->get();
					$place = ASM::$plm->get();
					if (CTR::$data->get('playerInfo')->get('color') != $place->getPlayerColor()) {
						ASM::$plm->load(array('id' => $commander->getRBase()));
						$home = ASM::$plm->getById($commander->getRBase());

						$duration = Game::getTimeToTravel($home, $place);
						$PAToTravel = Game::getPAToTravel($duration);

						if (CTR::$data->get('playerInfo')->get('actionPoint') >= $PAToTravel) {
							$creditsToTravel = ($obQuantity + $coloQuantity) * CREDITCOEFFTOCONQUER;
							
							if (CTR::$data->get('playerInfo')->get('credit') >= $creditsToTravel) {

								if ($commander->move($place->getId(), COM_COLO, $duration)) {
									$S_PAM1 = ASM::$pam->getCurrentSession();
									ASM::$pam->newSession(ASM_UMODE);
									ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
									$player = ASM::$pam->get();
									$player->decreaseCredit($creditsToTravel);
									$player->setActionPoint($player->getActionPoint() - $PAToTravel);
									ASM::$pam->changeSession($S_PAM1);
									CTR::$alert->add('Flotte envoyée.', ALERT_STD_SUCCESS);
								}
							} else {
								CTR::$alert->add('Vous n\'avez pas assez de crédits.', ALERT_STD_ERROR);
							}
						} else {
							CTR::$alert->add('Vous n\'avez pas assez de points d\'attaque.', ALERT_STD_ERROR);
						}
					} else {
						CTR::$alert->add('Vous ne pouvez pas attaquer un joueur de votre faction.', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('Ce lieu n\'existe pas.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Ce commandant ne vous appartient pas ou n\'existe pas.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Le niveau de la technologie ' . TechnologyResource::getInfo(Technology::BASE_QUANTITY, 'name') . ' n\'est pas assez élevé.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Vous n\'avez pas développé la technologie ' . TechnologyResource::getInfo(Technology::CONQUEST, 'name') . '.', ALERT_STD_ERROR);
	}
	ASM::$com->changeSession($S_COM1);
	ASM::$plm->changeSession($S_PLM1);
} else {
	CTR::$alert->add('Manque de précision sur le commandant ou la position.', ALERT_STD_ERROR);
}