<?php

/**
 * TutorialHelper
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @update 25.04.14
 */

class TutorialHelper {
	public static function checkTutorial() {
		# PAS UTILISEE POUR L'INSTANT (le sera quand il y aura une étape passive dans le tutoriel)
		$player = CTR::$data->get('playerId');
		$stepTutorial = CTR::$data->get('playerInfo')->get('stepTutorial');
		$stepDone = CTR::$data->get('playerInfo')->get('stepDone');

		if ($stepTutorial > 0) {
			if ($stepDone == FALSE) {
				# check if current step is done

				# hint : checker seulement les actions passives
				switch ($stepTutorial) {
					case 1:
						$asdf = 'asdf';
						break;
					case 2:
						$jlk = 'jkl';
						break;
				}
			} 
		}
	}

	public static function setStepDone() {

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		ASM::$pam->get()->stepDone = TRUE;

		CTR::$data->get('playerInfo')->add('stepDone', TRUE);

		ASM::$pam->changeSession($S_PAM1);
	}

	public static function clearStepDone() {

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		ASM::$pam->get()->stepDone = FALSE;

		CTR::$data->get('playerInfo')->add('stepDone', FALSE);

		ASM::$pam->changeSession($S_PAM1);
	}

	public static function isNextBuildingStepAlreadyDone($playerId, $buildingId, $level) {
		$nextStepAlreadyDone = FALSE;

		$S_OBM2 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlayer' => $playerId));
		for ($i = 0; $i < ASM::$obm->size() ; $i++) { 
			$ob = ASM::$obm->get($i);
			if ($ob->getBuildingLevel($buildingId) >= $level) {
				$nextStepAlreadyDone = TRUE;
				break;
			} else {
				# verify in the queue
				$S_BQM2 = ASM::$bqm->getCurrentSession();
				ASM::$bqm->newSession();
				ASM::$bqm->load(array('rOrbitalBase' => $ob->rPlace));
				for ($i = 0; $i < ASM::$bqm->size() ; $i++) { 
					$bq = ASM::$bqm->get($i);
					if ($bq->buildingNumber == $buildingId AND $bq->targetLevel >= $level) {
						$nextStepAlreadyDone = TRUE;
						break;
					} 
				}
				ASM::$bqm->changeSession($S_BQM2);
			}
		}
		ASM::$obm->changeSession($S_OBM2);

		return $nextStepAlreadyDone;
	}

	public static function isNextTechnoStepAlreadyDone($playerId, $technoId, $level = 1) {
		$nextStepAlreadyDone = FALSE;

		include_once PROMETHEE;
		$tech = new Technology($playerId);
		if ($tech->getTechnology($technoId) >= $level) {
			$nextStepAlreadyDone = TRUE;
		} else {
			# verify in the queue
			$S_TQM2 = ASM::$tqm->getCurrentSession();
			ASM::$tqm->newSession();
			ASM::$tqm->load(array('rPlayer' => $playerId));
			for ($i = 0; $i < ASM::$tqm->size() ; $i++) { 
				$tq = ASM::$tqm->get($i);
				if ($tq->technology == $technoId) {
					$nextStepAlreadyDone = TRUE;
					break;
				} 
			}
			ASM::$tqm->changeSession($S_TQM2);
		}

		return $nextStepAlreadyDone;
	}
}
?>