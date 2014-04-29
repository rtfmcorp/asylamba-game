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
}
?>