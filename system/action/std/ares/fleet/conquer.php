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
		ASM::$com->load(array('c.rPlayer' => CTR::$data->get('playerId'), 'c.statement' => Commander::MOVING));
		for ($i = 0; $i < ASM::$com->size(); $i++) { 
			if (ASM::$com->get($i)->travelType == Commander::COLO) {
				$coloQuantity++;
			}
		}
		ASM::$com->changeSession($S_COM2);
		$totalBases = $obQuantity + $msQuantity + $coloQuantity;
		if ($totalBases < $maxBasesQuantity) {

			$S_PAM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession(ASM_UMODE);
			ASM::$pam->load(array('id' => ASM::$plm->get()->rPlayer));

			if (ASM::$pam->get()->level > 3 || ASM::$pam->get()->statement >= PAM_DELETED) {
				if (ASM::$com->size() > 0) {
					if (ASM::$plm->size() > 0) {
						$commander = ASM::$com->get();
						$place = ASM::$plm->get();

						if (CTR::$data->get('playerInfo')->get('color') != $place->getPlayerColor()) {
							ASM::$plm->load(array('id' => $commander->getRBase()));
							$home = ASM::$plm->getById($commander->getRBase());

							$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
							$duration = Game::getTimeToTravel($home, $place, CTR::$data->get('playerBonus'));

							# compute price
							$price = $totalBases * CREDITCOEFFTOCONQUER;
							if (CTR::$data->get('playerInfo')->get('color') == ColorResource::CARDAN) {
								# bonus if the player is from Cardan
								$price -= round($price * ColorResource::BONUS_CARDAN_COLO / 100);
							}
							if (CTR::$data->get('playerInfo')->get('credit') >= $price) {
								if ($commander->getPev() > 0) {
									if ($commander->statement == Commander::AFFECTED) {
										if ($duration <= Commander::MAXTRAVELTIME) {
											if ($commander->move($place->getId(), $commander->rBase, Commander::COLO, $length, $duration)) {
												# debit credit
												$S_PAM2 = ASM::$pam->getCurrentSession();
												ASM::$pam->newSession(ASM_UMODE);
												ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
												ASM::$pam->get()->decreaseCredit($price);
												ASM::$pam->changeSession($S_PAM2);

												#CTR::$alert->add('Flotte envoyée.', ALERT_STD_SUCCESS);

												if (CTR::$get->exist('redirect')) {
													CTR::redirect('map/place-' . CTR::$get->get('redirect'));
												}
											}
										} else {
											CTR::$alert->add('Cet emplacement est trop éloigné.', ALERT_STD_ERROR);	
										}
									} else {
										CTR::$alert->add('Cet officier est déjà en déplacement.', ALERT_STD_ERROR);	
									}
								} else {
									CTR::$alert->add('Vous devez affecter au moins un vaisseau à votre officier.', ALERT_STD_ERROR);	
								}
							} else {
								CTR::$alert->add('Vous n\'avez pas assez de crédits pour conquérir cette base.', ALERT_STD_ERROR);
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
				CTR::$alert->add('Vous ne pouvez pas conquérir un joueur de niveau 3 ou moins.', ALERT_STD_ERROR);
			}
			ASM::$pam->changeSession($S_PAM1);
		} else {
			CTR::$alert->add('Vous avez assez de conquête en cours ou un niveau d\'administration étendue trop faible.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Vous devez débloquer la technologie de conquête.', ALERT_STD_ERROR);
	}
	ASM::$plm->changeSession($S_PLM1);
	ASM::$com->changeSession($S_COM1);
} else {
	CTR::$alert->add('Manque de précision sur le commandant ou la position.', ALERT_STD_ERROR);
}