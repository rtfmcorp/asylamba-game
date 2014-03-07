<?php
include_once ATHENA;
include_once ZEUS;
# change the type of the base action

# int baseid 		id of the orbital base
# int type			new type for the base

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (CTR::$get->exist('baseid')) {
	$baseId = CTR::$get->get('baseid');
} elseif (CTR::$post->exist('baseid')) {
	$baseId = CTR::$post->get('baseid');
} else {
	$baseId = FALSE;
}

if (CTR::$get->exist('type')) {
	$type = CTR::$get->get('type');
} elseif (CTR::$post->exist('type')) {
	$type = CTR::$post->get('type');
} else {
	$type = FALSE;
}

if ($baseId !== FALSE AND $type !== FALSE AND in_array($baseId, $verif)) {
 	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlace' => $baseId));
	$orbitalBase = ASM::$obm->get();

	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
	$player = ASM::$pam->get();

	if ($orbitalBase->typeOfBase == OrbitalBase::TYP_NEUTRAL) {
		if ($orbitalBase->levelGenerator >= OBM_LEVEL_MIN_TO_CHANGE_TYPE) {
			switch ($type) {
				case OrbitalBase::TYP_COMMERCIAL:
					if ($player->credit >= OBM_PRICE_FOR_COMMERCIAL) {
						$orbitalBase->typeOfBase = $type;
						$player->decreaseCredit(OBM_PRICE_FOR_COMMERCIAL);
						CTR::$alert->add($orbitalBase->name . ' est désormais un Centre Industriel', ALERT_STD_SUCCESS);
					} else {
						CTR::$alert->add('Evolution de votre colonie impossible - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
					}
					break;
				case OrbitalBase::TYP_MILITARY:
					if ($player->credit >= OBM_PRICE_FOR_MILITARY) {
						$orbitalBase->typeOfBase = $type;
						$player->decreaseCredit(OBM_PRICE_FOR_MILITARY);
						CTR::$alert->add($orbitalBase->name . ' est désormais une Base Militaire', ALERT_STD_SUCCESS);
					} else {
						CTR::$alert->add('Evolution de votre colonie impossible - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
					}
					break;
				default :
					CTR::$alert->add('Modification du type de la base orbitale impossible (seulement commercial ou militaire)', ALERT_STD_ERROR);
					break;
			}
		} else {
			CTR::$alert->add('Evolution de votre colonie impossible - niveau du générateur pas assez élevé', ALERT_STD_ERROR);
		}
	} elseif ($orbitalBase->typeOfBase == OrbitalBase::TYP_COMMERCIAL OR $orbitalBase->typeOfBase == OrbitalBase::TYP_MILITARY) {
		if ($type == OrbitalBase::TYP_CAPITAL) {
			$S_OBM2 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession();
			ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')));
			$alreadyACapital = FALSE;
			for ($i = 0; $i < ASM::$obm->size(); $i++) { 
				if (ASM::$obm->get($i)->typeOfBase == OrbitalBase::TYP_CAPITAL) {
					$alreadyACapital = TRUE;
					break;
				}
			}
			ASM::$obm->changeSession($S_OBM2);

			if ($alreadyACapital) {
				CTR::$alert->add('Evolution de votre planète impossible - vous avez déjà une capitale !', ALERT_STD_ERROR);
			} else {
				if ($player->credit >= OBM_PRICE_FOR_CAPITAL) {
					$orbitalBase->typeOfBase = $type;
					$player->decreaseCredit(OBM_PRICE_FOR_CAPITAL);
					CTR::$alert->add($orbitalBase->name . ' est désormais une capitale.', ALERT_STD_SUCCESS);
				} else {
					CTR::$alert->add('Modification du type de la base orbitale impossible - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
				}
			}
		} elseif (($orbitalBase->typeOfBase == OrbitalBase::TYP_COMMERCIAL AND $type == OrbitalBase::TYP_MILITARY)
			OR ($orbitalBase->typeOfBase == OrbitalBase::TYP_MILITARY AND $type == OrbitalBase::TYP_COMMERCIAL)) {
			# commercial --> military OR military --> commercial
			if ($player->credit >= OBM_PRICE_FOR_DESTRUCTION) {
				$player->decreaseCredit(OBM_PRICE_FOR_DESTRUCTION);
				# delete commercial buildings
				for ($i = 0; $i < 8; $i++) { 
					$maxLevel = OrbitalBaseResource::getBuildingInfo($i, 'maxLevel', $type);
					if ($orbitalBase->getBuildingLevel($i) > $maxLevel) {
						$orbitalBase->setBuildingLevel($i, $maxLevel);
					}
				}
				# delete buildings in queue
				$S_BQM1 = ASM::$bqm->getCurrentSession();
				ASM::$bqm->newSession(ASM_UMODE);
				ASM::$bqm->load(array('rOrbitalBase' => $baseId), array('dEnd'));
				for ($i = ASM::$bqm->size() - 1; $i >= 0; $i++) { 
					ASM::$bqm->deleteById(ASM::$bqm->get($i)->id);
				}
				ASM::$bqm->changeSession($S_BQM1);
				# send the right alert
				if ($type == OrbitalBase::TYP_COMMERCIAL) {
					CTR::$alert->add('Votre base orbitale devient commerciale. Vos bâtiments militaires sont détruits.', ALERT_STD_SUCCESS);
				} else {
					CTR::$alert->add('Votre base orbitale devient militaire. Vos bâtiments commerciaux sont détruits.', ALERT_STD_SUCCESS);
				}
			} else {
				CTR::$alert->add('modification du type de la base orbitale impossible - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('modification du type de la base orbitale impossible (seulement capitale, commercial ou militaire)', ALERT_STD_ERROR);
		}
	} elseif ($orbitalBase->typeOfBase == OrbitalBase::TYP_CAPITAL) {
		/*switch ($type) {
			case OrbitalBase::TYP_COMMERCIAL:
				$orbitalBase->typeOfBase = $type;
				# casser les bâtiments en trop
				# killer la file de construction
				CTR::$alert->add('Votre base orbitale devient commerciale.', ALERT_STD_SUCCESS);
				break;
			case OrbitalBase::TYP_MILITARY:
				$orbitalBase->typeOfBase = $type;
				# casser les bâtiments en trop
				# killer la file de construction
				CTR::$alert->add('Votre base orbitale devient militaire.', ALERT_STD_SUCCESS);
				break;
			default :
				CTR::$alert->add('modification du type de la base orbitale impossible (seulement commercial ou militaire)', ALERT_STD_ERROR);
				break;
		}*/
		CTR::$alert->add('modification du type de la base orbitale impossible - c\'est déjà une capitale !', ALERT_STD_ERROR);
	} else {
		CTR::$alert->add('modification du type de la base orbitale impossible - type invalide', ALERT_STD_ERROR);
	}
	ASM::$pam->changeSession($S_PAM1);
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour changer le type de la base orbitale', ALERT_STD_FILLFORM);
}
?>
