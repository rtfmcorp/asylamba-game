<?php
include_once ZEUS;
include_once ATHENA;
# validate tutorial step action

$playerId = CTR::$data->get('playerId');
$stepTutorial = CTR::$data->get('playerInfo')->get('stepTutorial');
$stepDone = CTR::$data->get('playerInfo')->get('stepDone');

if ($stepDone == TRUE AND TutorialResource::stepExists($stepTutorial)) {
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => $playerId));
	$player = ASM::$pam->get();

	$experience = TutorialResource::getInfo($stepTutorial, 'experienceReward');
	$credit = TutorialResource::getInfo($stepTutorial, 'creditReward');
	$resource = TutorialResource::getInfo($stepTutorial, 'resourceReward');
	$ship = TutorialResource::getInfo($stepTutorial, 'shipReward');

	$alert = 'Etape validée. ';

	$firstReward = true;
	if ($experience > 0) {
		$firstReward = false;
		$alert .= 'Vous gagnez ' . $experience . ' points d\'expérience';
		$player->increaseExperience($experience);
	}

	if ($credit > 0) {
		if ($firstReward) {
			$firstReward = false;
			$alert .= 'Vous gagnez ' . $credit . 'crédits';
		} else {
			$alert .= ', ainsi que ' . $credit . ' crédits';
		}
		$player->increaseCredit($credit);
	}

	if ($resource > 0 || $ship != array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)) {
		# load an orbital base of the player
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlayer' => $player->id));
		$ob = ASM::$obm->get();

		if ($resource > 0) {
			if ($firstReward) {
				$firstReward = false;
				$alert .= 'Vous gagnez ' . $resource . ' ressources';
			} else {
				$alert .= ' et ' . $resource . ' ressources';
			}
			$alert .= ' sur votre base orbitale ' . $ob->name . '. ';
			$ob->increaseResources($resource);
		}

		if ($ship != array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)) {
			$qty = 0;
			$ships = array();
			foreach ($ship as $key => $value) {
				if ($value != 0) {
					$ships[$qty] = array();
					$ships[$qty]['quantity'] = $value;
					$ships[$qty]['name'] = ShipResource::getInfo($key, 'codeName');
					$qty++;

					# add ship to dock
					$ob->addShipToDock($key, $value);
				}
			}
			if ($firstReward) {
				$firstReward = false;
				$alert .= 'Vous gagnez ';
				$endOfAlert = ' sur votre base orbitale ' . $ob->name . '. ';
			} else {
				$alert .= '. Vous gagnez également ';
				$endOfAlert = '. ';
			}

			# complete alert
			foreach ($ships as $key => $value) {
				if ($key == 0) {
					$alert .= $value['quantity'] . ' ' . $value['name'] . Format::plural($value['quantity']);
				} else if ($qty - 1 == $key) {
					$alert .= ' et ' . $value['quantity'] . ' ' . $value['name'] . Format::plural($value['quantity']);
				} else {
					$alert .= ', ' . $value['quantity'] . ' ' . $value['name'] . Format::plural($value['quantity']);
				}
			}
			$alert .= $endOfAlert;
		}

		ASM::$obm->changeSession($S_OBM1);
	} else {
		$alert .= '. ';
	}

	$alert .= 'La prochaine étape vous attend.';
	CTR::$alert->add($alert, ALERT_STD_SUCCESS);
	
	$nextStep = $stepTutorial;
	if (TutorialResource::isLastStep($stepTutorial)) {
		$nextStep = 0;
		CTR::$alert->add('Bravo, vous avez terminé le tutoriel. Bonne continuation et bon amusement sur Asylamba, vous pouvez maintenant voler de vos propres ailes !', ALERT_STD_SUCCESS);
	} else {
		$nextStep += 1;
	}

	# verify if the next step is already done
	$nextStepAlreadyDone = FALSE;
	$redirectWithoutJeanMi = FALSE;
	switch ($nextStep) {
		case TutorialResource::NAVIGATION :
			$redirectWithoutJeanMi = TRUE;
			$nextStepAlreadyDone = TRUE;
			break;
		case TutorialResource::GENERATOR_LEVEL_2 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::GENERATOR, 2);
			break;
		case TutorialResource::REFINERY_LEVEL_3 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::REFINERY, 3);
			break;
		case TutorialResource::STORAGE_LEVEL_3 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::STORAGE, 3);
			break;
		case TutorialResource::TECHNOSPHERE_LEVEL_1 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::TECHNOSPHERE, 1);
			break;
		case TutorialResource::MODIFY_UNI_INVEST:
			# asdf
			break;
		case TutorialResource::CREATE_COMMANDER:
			# asdf
			break;
		case TutorialResource::DOCK1_LEVEL_1 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::DOCK1, 1);
			break;
		case TutorialResource::SHIP0_UNBLOCK :
			$nextStepAlreadyDone = TutorialHelper::isNextTechnoStepAlreadyDone($playerId, Technology::SHIP0_UNBLOCK);
			break;
		case TutorialResource::BUILD_SHIP0 :
			# verify in the queue
			$S_SQM2 = ASM::$sqm->getCurrentSession();
			ASM::$sqm->newSession();

			$S_OBM2 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession();
			ASM::$obm->load(array('rPlayer' => $playerId));

			# load the queues
			for ($i = 0; $i < ASM::$obm->size() ; $i++) { 
				$ob = ASM::$obm->get($i);
				ASM::$sqm->load(array('rOrbitalBase' => $ob->rPlace));
			}
			ASM::$obm->changeSession($S_OBM2);

			for ($i = 0; $i < ASM::$sqm->size() ; $i++) { 
				$sq = ASM::$sqm->get($i);
				if ($sq->shipNumber == ShipResource::PEGASE) {
					$nextStepAlreadyDone = TRUE;
					break;
				} 
			}
			ASM::$sqm->changeSession($S_SQM2);
			break;
		case TutorialResource::AFFECT_COMMANDER:
			# asdf
			break;
		case TutorialResource::FILL_SQUADRON :
			# asdf
			break;
		case TutorialResource::MOVE_FLEET_LINE :
			# asdf
			break;
		case TutorialResource::SPY_PLANET :
			# asdf
			break;
		case TutorialResource::LOOT_PLANET :
			# asdf
			break;
		case TutorialResource::REFINERY_LEVEL_10 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::REFINERY, 10);
			break;
		case TutorialResource::STORAGE_LEVEL_8 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::STORAGE, 8);
			break;
		case TutorialResource::DOCK1_LEVEL_6 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::DOCK1, 6);
			break;
		case TutorialResource::REFINERY_LEVEL_16 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::REFINERY, 16);
			break;
		case TutorialResource::STORAGE_LEVEL_12 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::STORAGE, 12);
			break;
		case TutorialResource::TECHNOSPHERE_LEVEL_6 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::TECHNOSPHERE, 6);
			break;
		case TutorialResource::SHIP1_UNBLOCK :
			$nextStepAlreadyDone = TutorialHelper::isNextTechnoStepAlreadyDone($playerId, Technology::SHIP1_UNBLOCK);
			break;
		case TutorialResource::DOCK1_LEVEL_15 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::DOCK1, 15);
			break;
		case TutorialResource::BUILD_SHIP1 :
			# asdf
			break;
		case TutorialResource::REFINERY_LEVEL_20 :
			$nextStepAlreadyDone = TutorialHelper::isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::REFINERY, 20);
			break;
	}

	if (!$nextStepAlreadyDone) {
		$player->stepDone = FALSE;
		CTR::$data->get('playerInfo')->add('stepDone', FALSE);
	}
	$player->stepTutorial = $nextStep;
	CTR::$data->get('playerInfo')->add('stepTutorial', $nextStep);

	ASM::$pam->changeSession($S_PAM1);

	if ($redirectWithoutJeanMi) {
		CTR::redirect('profil');
	}
} else {
	CTR::$alert->add('Impossible de valider l\'étape avant de l\'avoir effectuée.', ALERT_STD_FILLFORM);
}
?>