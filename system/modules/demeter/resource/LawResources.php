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
			'devName' => 'taxes',
			'name' => 'modification des taux d\'imposition',
			'department' => 4,
			'shortDescription' => 'modifier le taux d\'imposition de votre faction',
			'longDescription' =>'',
			'image' => ''),
	);

	public static function getInfo($id, $info) {
		if ($id <= self::size()) {
			if (in_array($info, array('id', 'devName', 'name', 'shortDescription', 'longDescription', 'image'))) {
				return self::$forums[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function getInfoForId($id, $info) {
		if (in_array($info, array('id', 'devName', 'name', 'shortDescription', 'longDescription', 'image'))) {
			$tmp = array_keys(self::$idLink, $id);
			return self::$forums[$tmp[0]][$info];
		} else {
			return FALSE;
		}
	}

	public static function size() { return count(self::$laws); }
}