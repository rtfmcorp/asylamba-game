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
		$player = CTR::$data->get('playerId');
		$stepTutorial = CTR::$data->get('playerInfo')->get('stepTutorial');
		$stepDone = CTR::$data->get('playerInfo')->get('stepDone');

		if ($stepTutorial != -1) {
			if ($stepDone == FALSE) {
				# check if current step is done
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
}
?>