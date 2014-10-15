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
			'devName' => 'sector taxes',
			'name' => 'modification des taux d\'imposition',
			'department' => 3,
			'price' => 1000,
			'duration' => 0,
			'shortDescription' => 'modifier le taux d\'imposition de la population des planètes d\'un secteur de votre faction entre 2 et 15',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 2,
			'devName' => 'secteurName',
			'name' => 'modification du nom d\'un secteur',
			'department' => 6,
			'price' => 1000,
			'duration' => 0,
			'shortDescription' => 'modifier le nom d\'un secteur.',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 3,
			'devName' => 'commercial taxes export',
			'name' => 'modification des taux de taxes commerciale en importation',
			'department' => 3,
			'price' => 1000,
			'duration' => 0,
			'shortDescription' => 'modifier le taux de taxes commerciale d\'importation avec une autre faction entre 2 et 15 (peut-être plus basse pour les taxes internes)',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 4,
			'devName' => 'commercial taxes import',
			'name' => 'modification des taux de taxes commerciale en exportation',
			'department' => 3,
			'price' => 1000,
			'duration' => 0,
			'shortDescription' => 'modifier le taux de taxes commerciale d\'exportation avec une autre faction entre 2 et 15 (peut-être plus basse pour les taxes internes)',
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