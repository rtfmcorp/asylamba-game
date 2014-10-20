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
			'bonusLaw' => FALSE,
			'devName' => 'sector taxes',
			'name' => 'Ajustement de l\'imposition sectoriel',
			'department' => 3,
			'price' => 1000,
			'duration' => 0,
			'bonus' => 0,
			'shortDescription' => 'Cette loi modifie le taux d\'imposition appliquée au planètes d\'un secteur en votre possession. La redevance perçue provient autant des bases de votre faction que celles de factions ennemies. (entre 2 et 15)',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 2,
			'bonusLaw' => FALSE,
			'devName' => 'secteurName',
			'name' => 'Décret de revendiquation',
			'department' => 6,
			'price' => 1000,
			'duration' => 0,
			'bonus' => 0,
			'shortDescription' => 'Afin de montrer à tous la puissance de votre faction et d\'asseoire la conquête d\'un nouveau territoire, ce décret permet de modifier le nom d\'un secteur.',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 3,
			'bonusLaw' => FALSE,
			'devName' => 'commercial taxes export',
			'name' => 'Ajustement des taxes d\'achat',
			'department' => 3,
			'price' => 1000,
			'duration' => 0,
			'bonus' => 0,
			'shortDescription' => 'modifier le taux de taxes commerciale d\'importation avec une autre faction entre 2 et 15 (peut-être plus basse pour les taxes internes)',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 4,
			'bonusLaw' => FALSE,
			'devName' => 'commercial taxes import',
			'name' => 'Ajustement des taxes de vente',
			'department' => 3,
			'price' => 1000,
			'duration' => 0,
			'bonus' => 0,
			'shortDescription' => 'modifier le taux de taxes commerciale d\'exportation avec une autre faction entre 2 et 15 (peut-être plus basse pour les taxes internes)',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 5,
			'bonusLaw' => TRUE,
			'devName' => 'military subvention',
			'name' => 'Subventions militaires',
			'department' => 4,
			'price' => 1000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'diminue le cout des vaisseaux de 10% pendant 1 semaine',
			'longDescription' => '',
			'image' => ''),
		array(
			'id' => 6,
			'bonusLaw' => TRUE,
			'devName' => 'technology',
			'name' => 'Transfert technologique',
			'department' => 5,
			'price' => 1000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'augmente la vitesse de développement d\'une technology de 10% pendant 1 semaine)',
			'longDescription' => '',
			'image' => ''),
	);

	public static function getInfo($id, $info) {
		if ($id <= self::size()) {
			if (in_array($info, array('id', 'bonusLaw', 'devName', 'name', 'department', 'price', 'duration', 'bonus', 'shortDescription', 'longDescription', 'image'))) {
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