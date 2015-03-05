<?php

/**
 * Ressources pour conditions de victoires
 *
 * @author Gil Clavien
 * @copyright Asylamba
 *
 * @package Demeter
 * @update 05.03.15
*/

class VictoryResources {
	private static $victories = [
		[
			'title' => 'Victoire de prestige',
			'infos' => 'La victoire de prestige est atteinte lorsque votre faction domine suffisamment de territoire pour se revendiquer comme la plus importante puissance de la galaxie.',
			'targets' => [
				[
					'label' => 'Contrôler au minimum 12 secteurs différents.',
					'nb' => 12,
					'sectors' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36]
				]
			]
		], [
			'title' => 'Victoire tactique',
			'infos' => 'La victoire tactique est atteinte lorsque votre faction possède suffisamment de points-clés dans la galaxie lui permettant d\'intervenir rapidement sur les zones rebelles.',
			'targets' => [
				[
					'label' => 'Contrôler au minimum 8 secteurs différents.',
					'nb' => 8,
					'sectors' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36]
				], [
					'label' => 'Contrôler le secteur central.',
					'nb' => 1,
					'sectors' => [1]
				], [
					'label' => 'Contrôler au minimum 2 passerelles.',
					'nb' => 2,
					'sectors' => [9, 10, 11, 12, 13, 14, 15]
				]
			]
		]
	];

	public static function getInfo($id, $info) {
		if ($id <= self::size()) {
			return self::$victories[$id - 1][$info];
		} else {
			return FALSE;
		}
	}

	public static function size() { return count(self::$victories); }
}