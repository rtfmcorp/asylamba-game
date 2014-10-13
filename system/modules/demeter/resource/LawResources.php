<?php

/**
 * ressources pour les lois
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 29.09.14
*/

#id des forums : < 10 = pour tous les gens d'une faction, >= 10 < 20 = pour le gouvernement d'une fac, >= 20 pour les chefs de toutes les factions
Class LawResources {

	private static $laws = array(
		array(
			'id' => 1,
			'devName' => 'faction taxes',
			'name' => 'modification des taux d\'imposition',
			'department' => 3,
			'price' => 1000,
			'duration' => 0,
			'shortDescription' => 'modifier le taux d\'imposition de la population des planètes de votre faction',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 2,
			'devName' => 'taxes',
			'name' => 'modification des taxes comerciales',
			'department' => 3,
			'price' => 1000,
			'duration' => 0,
			'shortDescription' => 'modifier les taxes commerciale dans un secteur.',
			'longDescription' => '',
			'image' => ''),
	);

	public static function getInfo($id, $info) {
		if ($id <= self::size()) {
			if (in_array($info, array('id', 'devName', 'name', 'department', 'price', 'duration', 'shortDescription', 'longDescription', 'image'))) {
				return self::$laws[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function size() { return count(self::$laws); }
}