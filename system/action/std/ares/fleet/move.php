<?php
include_once ARES;
include_once GAIA;
include_once ZEUS;
# send a fleet to move to a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place de destination

$commanderId = Utils::getHTTPData('commanderid');
$placeId = Utils::getHTTPData('placeid');


if ($commanderId !== FALSE AND $placeId !== FALSE) {
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('c.id' => $commanderId, 'c.rPlayer' => CTR::$data->get('playerId')));
	$commander = ASM::$com->get();
	
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession();
	ASM::$plm->load(array('id' => $placeId));
	$place = ASM::$plm->get();

	if (ASM::$com->size() > 0) {
		if (ASM::$plm->size() > 0) {
			if ($commander->playerColor == $place->playerColor) {
				ASM::$plm->load(array('id' => $commander->getRBase()));
				$home = ASM::$plm->getById($commander->getRBase());

				$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
				$duration = Game::getTimeToTravel($home, $place, CTR::$data->get('playerBonus'));
			
				if ($commander->statement == Commander::AFFECTED) {
					$S_SEM = ASM::$sem->getCurrentSession();
					ASM::$sem->newSession();
					ASM::$sem->load(array('id' => $place->rSector));
					$isFactionSector = (ASM::$sem->get()->rColor == $commander->playerColor) ? TRUE : FALSE;
					ASM::$sem->changeSession($S_SEM);
					
					$commander->destinationPlaceName = $place->baseName;
					if ($length <= Commander::DISTANCEMAX || $isFactionSector) {
						$commander->move($place->getId(), $commander->rBase, Commander::MOVE, $length, $duration);
					} else {
						CTR::$alert->add('Cet emplacement est trop éloigné.', ALERT_STD_ERROR);	
					}
				} else {
					CTR::$alert->add('Cet officier est déjà en déplacement.', ALERT_STD_ERROR);	
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
	ASM::$com->changeSession($S_COM1);
	ASM::$plm->changeSession($S_PLM1);
} else {
	CTR::$alert->add('Manque de précision sur le commandant ou la position.', ALERT_STD_ERROR);
}