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
	private static $colors = array(
	);

	public static function getInfo($id, $info) {
		if ($id <= self::size()) {
			if (in_array($info, array(''))) {
				return self::$colors[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function size() { return count(self::$colors); }
}