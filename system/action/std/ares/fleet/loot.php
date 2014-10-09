<?php
include_once ARES;
include_once GAIA;
include_once ZEUS;
# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

$commanderId = Utils::getHTTPData('commanderid');
$placeId = Utils::getHTTPData('placeid');

if ($commanderId !== FALSE AND $placeId !== FALSE) {
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession(ASM_UMODE);
	ASM::$com->load(array('c.id' => $commanderId, 'c.rPlayer' => CTR::$data->get('playerId')));
	
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession(ASM_UMODE);
	ASM::$plm->load(array('id' => $placeId));

	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession(ASM_UMODE);
	ASM::$pam->load(array('id' => ASM::$plm->get()->rPlayer));

	if (ASM::$pam->size() == 0) {
		if (ASM::$com->size() > 0) {
			if (ASM::$plm->size() > 0) {
				$commander = ASM::$com->get();
				$place = ASM::$plm->get();
				if (CTR::$data->get('playerInfo')->get('color') != $place->getPlayerColor()) {
					ASM::$plm->load(array('id' => $commander->getRBase()));
					$home = ASM::$plm->getById($commander->getRBase());

					$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
					$duration = Game::getTimeToTravel($home, $place, CTR::$data->get('playerBonus'));

					if ($commander->getPev() > 0) {
						if ($commander->move($place->getId(), $commander->rBase, Commander::LOOT, $length, $duration)) {

							if (CTR::$get->exist('redirect')) {
								CTR::redirect('map/place-' . CTR::$get->get('redirect'));
							}
						}
					} else {
						CTR::$alert->add('Vous devez affecter au moins un vaisseau à votre officier.', ALERT_STD_ERROR);	
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
	} elseif (ASM::$pam->get()->level > 1 || ASM::$pam->get()->statement >= PAM_DELETED) {
		if (ASM::$com->size() > 0) {
			if (ASM::$plm->size() > 0) {
				$commander = ASM::$com->get();
				$place = ASM::$plm->get();

				if (CTR::$data->get('playerInfo')->get('color') != $place->getPlayerColor()) {
					ASM::$plm->load(array('id' => $commander->getRBase()));
					$home = ASM::$plm->getById($commander->getRBase());

					$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
					$duration = Game::getTimeToTravel($home, $place, CTR::$data->get('playerBonus'));

					if ($commander->getPev() > 0) {
						if ($commander->move($place->getId(), $commander->rBase, Commander::LOOT, $length, $duration)) {
							$commander->dStart = Utils::now();
							CTR::$alert->add('Flotte envoyée.', ALERT_STD_SUCCESS);

							if (CTR::$get->exist('redirect')) {
								CTR::redirect('map/place-' . CTR::$get->get('redirect'));
							}
						}
					} else {
						CTR::$alert->add('Vous devez affecter au moins un vaisseau à votre officier.', ALERT_STD_ERROR);	
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
	} else {
		CTR::$alert->add('Vous ne pouvez pas piller un joueur de niveau 1.', ALERT_STD_ERROR);
	}
	ASM::$pam->changeSession($S_PAM1);
	ASM::$plm->changeSession($S_PLM1);
	ASM::$com->changeSession($S_COM1);
} else {
	CTR::$alert->add('Manque de précision sur le commandant ou la position.', ALERT_STD_ERROR);
}