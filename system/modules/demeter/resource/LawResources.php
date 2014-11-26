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
			'name' => 'Ajustement de l\'imposition sectorielle',
			'department' => 3,
			'price' => 50000,
			'duration' => 0,
			'bonus' => 0,
			'shortDescription' => 'Les planètes dans les secteurs sous votre contrôle vous paient un impôt et c\'est à la faction de choisir ce dernier.',
			'longDescription' => 'Cette loi modifie le taux d\'imposition appliqué aux planètes d\'un secteur en votre possession. La redevance perçue provient autant des bases de votre faction que de celles de factions ennemies.<br /><br />Un impôt faible dans vos secteurs faibles renforcera votre faction et un impôt fort dans les secteurs partagés permettra de prendre plus de crédits de joueurs ennemis.<br /><br />L\'impôt peut varier de 2% à 15%.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 2,
			'bonusLaw' => FALSE,
			'devName' => 'secteurName',
			'name' => 'Décret de revendication',
			'department' => 6,
			'price' => 250000,
			'duration' => 0,
			'bonus' => 0,
			'shortDescription' => 'Afin de montrer à tous la puissance de votre faction et d\'asseoir la conquête d\'un nouveau territoire, ce décret permet de modifier le nom d\'un secteur.',
			'longDescription' => 'Permet de modifier le nom d\'un secteur que vous possédez.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 3,
			'bonusLaw' => FALSE,
			'devName' => 'commercial taxes export',
			'name' => 'Ajustement des taxes de vente',
			'department' => 3,
			'price' => 50000,
			'duration' => 0,
			'bonus' => 0,
			'shortDescription' => 'Cette loi permet de modifier les taxes sur les ventes dans le marché. Elle permet de régler finement la taxe perçue par la faction lors de la vente d\'un bien en fonction de sa destination.',
			'longDescription' => 'Modifie le taux de la taxe à la vente de produit pour une faction précise.<br /><br />Un taux bas favorisera le commerce entre deux factions.<br /><br />L\'impôt peut varier de 2% à 15% entre deux factions différentes et de 0% à 15% au sein de la même faction.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 4,
			'bonusLaw' => FALSE,
			'devName' => 'commercial taxes import',
			'name' => 'Ajustement des taxes d\'achat',
			'department' => 3,
			'price' => 50000,
			'duration' => 0,
			'bonus' => 0,
			'shortDescription' => 'Cette loi permet de modifier les taxes sur les achats dans le marché. Elle permet de régler finement la taxe perçue par la faction lors de l\'achat d\'un bien en fonction de sa provenance.',
			'longDescription' => 'Modifie le taux de la taxe à l\'achat de produit d\'une faction précise.<br /><br />Un taux bas favorisera le commerce entre deux factions.<br /><br />L\'impôt peut varier de 2% à 15% entre deux factions différentes et de 0% à 15% au sein de la même faction.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 5,
			'bonusLaw' => TRUE,
			'devName' => 'military subvention',
			'name' => 'Subventions militaires',
			'department' => 4,
			'price' => 10000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'Pas encore active.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => FALSE),
		array(
			'id' => 6,
			'bonusLaw' => TRUE,
			'devName' => 'technology',
			'name' => 'Transfert technologique',
			'department' => 5,
			'price' => 2500000,
			'duration' => 108000, //une semaine entière
			'bonus' => 25,
			'shortDescription' => 'Augmente la vitesse de développement des technologies (dans la technosphère) de 25% pendant 30 relèves.',
			'longDescription' => 'Cette loi offre à tout les membres de la factions un bonus dans le développement de leur technologies de 25% (cumulé avec les autres bonus). La durée est de 30 relèves.<br /><br />Seules les recherches lancées lorsque le bonus est actif seront impactées.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 7,
			'bonusLaw' => TRUE,
			'devName' => 'trade agrements',
			'name' => 'Accords commerciaux',
			'department' => 3,
			'price' => 10000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'Pas encore active.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => FALSE),
		array(
			'id' => 7,
			'bonusLaw' => TRUE,
			'devName' => 'placement',
			'name' => 'Placement financier',
			'department' => 3,
			'price' => 10000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'Pas encore active.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => FALSE),
		array(
			'id' => 8,
			'bonusLaw' => TRUE,
			'devName' => 'startege',
			'name' => 'Positions défensives',
			'department' => 4,
			'price' => 10000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'Pas encore active.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => FALSE),
		array(
			'id' => 9,
			'bonusLaw' => TRUE,
			'devName' => 'navigation',
			'name' => 'Guerre éclaire',
			'department' => 4,
			'price' => 10000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'Pas encore active.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => FALSE),
		array(
			'id' => 10,
			'bonusLaw' => TRUE,
			'devName' => 'education',
			'name' => 'Décret sur l\'éducation',
			'department' => 5,
			'price' => 10000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'Pas encore active.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => FALSE),
		array(
			'id' => 11,
			'bonusLaw' => TRUE,
			'devName' => 'Subventions',
			'name' => 'Subventions universitaires',
			'department' => 5,
			'price' => 10000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'Pas encore active.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => FALSE),
		array(
			'id' => 12,
			'bonusLaw' => TRUE,
			'devName' => 'attaque',
			'name' => 'Force de frappe',
			'department' => 4,
			'price' => 10000,
			'duration' => 604800, //une semaine entière
			'bonus' => 10,
			'shortDescription' => 'Pas encore active.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => FALSE),
	);

	public static function getInfo($id, $info) {
		if ($id <= self::size()) {
			if (in_array($info, array('id', 'bonusLaw', 'devName', 'name', 'department', 'price', 'duration', 'bonus', 'shortDescription', 'longDescription', 'image', 'isImplemented'))) {
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