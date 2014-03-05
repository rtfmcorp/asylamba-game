<?php

/**
 * ressources pour les foruuüm
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/

Class ForumResources {
	private static $forums = array(
		array(
			'id' => 1,
			'devName' => 'main',
			'name' => 'Général',
			'shortDescription' => 'Discutez de tout et de rien',
			'longDescription' =>'',
			'image' => ''),
		array(
			'id' => 2,
			'devName' => 'noob',
			'name' => 'Aide',
			'shortDescription' => 'Nouveau ? Besoin d\'aide ? C\'est ici que ça se passe',
			'longDescription' =>'',
			'image' => ''),
		array(
			'id' => 3,
			'devName' => 'war',
			'name' => 'Plans de bataille',
			'shortDescription' => 'Planifiez vos batailles, écrasez vos ennemis',
			'longDescription' =>'',
			'image' => ''),
		array(
			'id' => 4,
			'devName' => 'politic',
			'name' => 'Politique',
			'shortDescription' => 'Présentez votre programme, votre vision de l\'avenir',
			'longDescription' =>'',
			'image' => ''),
		array(
			'id' => 5,
			'devName' => 'flood',
			'name' => 'Biastro',
			'shortDescription' => 'Buvez de la bière et racontez n\'importe quoi',
			'longDescription' =>'',
			'image' => ''),
		array(
			'id' => 6,
			'devName' => 'rp',
			'name' => 'Maison close',
			'shortDescription' => 'Ici, le roleplay est de rigueur',
			'longDescription' =>'',
			'image' => ''),
		array(
			'id' => 7,
			'devName' => 'rc',
			'name' => 'Maison du Commerce',
			'shortDescription' => 'Routes commerciales, échanges, prospection ou paris',
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

	public static function size() { return count(self::$forums); }
}