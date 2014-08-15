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

	$maxCom = ($place->typeOfOrbitalBase == 0) ? 2 : 5;

	if (count($place->commanders) < $maxCom) {
		if (ASM::$com->size() > 0) {
			if (ASM::$plm->size() > 0) {
				if (CTR::$data->get('playerId') == $place->getRPlayer()) {
					ASM::$plm->load(array('id' => $commander->getRBase()));
					$home = ASM::$plm->getById($commander->getRBase());

					$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
					
					$playerBonus = new PlayerBonus($commander->rPlayer);
					$playerBonus->load();
					$duration = Game::getTimeToTravel($home, $place, $playerBonus->bonus);

					if ($commander->move($place->getId(), $commander->rBase, Commander::MOVE, $length, $duration)) {
						CTR::$alert->add('Flotte envoyée.', ALERT_STD_SUCCESS);
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