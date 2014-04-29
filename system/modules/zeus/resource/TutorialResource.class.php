<?php

/**
 * TutorialResource
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @update 25.04.14
 */

class TutorialResource {

	const GENERATOR_LEVEL_2 = 1;
	const REFINERY_LEVEL_3 = 2;
	const REFINERY_MODE_PRODUCTION = 3;

	public static function stepExists($step) {
		if ($step > 0 AND $step <= count(self::$steps)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function isLastStep($step) {
		if ($step == count(self::$steps)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function getInfo($id, $info) {
		if ($id <= count(self::$steps)) {
			if (in_array($info, array('id', 'title', 'description', 'experienceReward'))) {
				return self::$steps[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	private static $steps = array(
		array(
			'id' => 1,
			'title' => 'Construire le générateur au niveau 2',
			'description' => 'asdf',
			'experienceReward' => 3),
		array(
			'id' => 2,
			'title' => 'Construire la raffinerie au niveau 3',
			'description' => 'asdf',
			'experienceReward' => 10),
		array(
			'id' => 3,
			'title' => 'Mettre la raffinerie en mode production',
			'description' => 'asdf',
			'experienceReward' => 15)
	);
}
?>