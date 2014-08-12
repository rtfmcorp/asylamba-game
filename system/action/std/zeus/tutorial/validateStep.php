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

	$player->stepTutorial = $nextStep;
	CTR::$data->get('playerInfo')->add('stepTutorial', $nextStep);
	$player->stepDone = FALSE;
	CTR::$data->get('playerInfo')->add('stepDone', FALSE);

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('Impossible de valider l\'étape avant de l\'avoir effectuée.', ALERT_STD_FILLFORM);
}
?>