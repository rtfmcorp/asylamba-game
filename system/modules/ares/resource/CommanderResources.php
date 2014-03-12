<?php

/**
 * ressources pour les commandants
 *
 * @author Gil Clavien
 * @copyright Expansion - le jeu
 *
 * @package Ares
 * @update 04.03.2014
*/

/*
Grand Amiral
Amiral
Vice-Amiral
Contre-Amiral
Commodore
Capitaine
Commandant
Lieutenant Commandant
Lieutenant
Sous-Lieutenant
Aspirant
*/
Class CommanderResources {
	private static $commanders = array(
		array(
			'grade' => 'Aspirant'
		),
		array(
			'grade' => 'Aspirant'
		),
		array(
			'grade' => 'Aspirant'
		),
		array(
			'grade' => 'Quartier-Maître'
		),
		array(
			'grade' => 'Sergeant'
		),
		array(
			'grade' => 'Enseigne'
		),
		array(
			'grade' => 'Lieutenant'
		),
		array(
			'grade' => 'Capitaine'
		),
		array(
			'grade' => 'Major'
		),
		array(
			'grade' => 'Colonel'
		),
		array(
			'grade' => 'Commandant'
		),
		array(
			'grade' => 'Commodore'
		),
		array(
			'grade' => 'Contre-Amiral'
		),
		array(
			'grade' => 'Vice-Amiral'
		),
		array(
			'grade' => 'Amiral'
		),
		array(
			'grade' => 'Grand Amiral'
		),
		array(
			'grade' => 'Grand Amiral'
		),
		array(
			'grade' => 'Grand Amiral'
		),
		array(
			'grade' => 'Grand Amiral'
		),
		array(
			'grade' => 'Grand Amiral'
		)
	);

	public static function getInfo($level, $info) {
		if ($level <= self::size()) {
			if (in_array($info, array('grade'))) {
				return self::$commanders[$level - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function size() {
		return count(self::$commanders);
	}
}
?>