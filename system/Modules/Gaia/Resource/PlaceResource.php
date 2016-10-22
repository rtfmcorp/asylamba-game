<?php

namespace Asylamba\Modules\Gaia\Resource;

class PlaceResource {
	public static function get($type, $info) {
		if (in_array($info, array('name', 'desc', 'price', 'school-size', 'tax', 'l-line', 'r-line', 'l-line-position', 'r-line-position'))) {
			return self::$place[$type][$info];
		} else {
			return FALSE;
		}
	}

	private static $place = array(
		array(
			'name' => 'Colonie',
			'desc' => 'Peu évoluée, la colonie vous donne accès aux bâtiments de base mais jusqu\'à un niveau peu élevé. Elle se trouvera généralement aux extrémités de votre empire et servira de relai pour l\'expansion de ce dernier.',

			'price' => 0,
			'school-size' => 3,

			'tax' => 1,
			'l-line' => 1,
			'l-line-position' => array(2),
			'r-line' => 1,
			'r-line-position' => array(2)
		),
		array(
			'name' => 'Centre Industriel',
			'desc' => 'Le centre industriel vous permet de produire beaucoup plus de ressouces en permettant d\'agrandir la raffinerie. Plus d\'impôts sont perçus à la population, vous avez donc plus de crédits à disposition à chaque relève. Les usines peuvent tourner nuit et jour et sont plus efficientes. Un plus grand impact commercial est possible.',

			'price' => 250000,
			'school-size' => 3,

			'tax' => 1.25,
			'l-line' => 1,
			'l-line-position' => array(2),
			'r-line' => 1,
			'r-line-position' => array(2)
		),
		array(
			'name' => 'Base Militaire',
			'desc' => 'Place forte militaire, cette planète dispose d\'un nombre important de flottes en orbite, prêtes à défendre ou à attaquer. Elle dispose en outre de très bons chantiers de construction de vaisseaux.',

			'price' => 250000,
			'school-size' => 8,

			'tax' => 0.5,
			'l-line' => 3,
			'l-line-position' => array(1, 2, 3),
			'r-line' => 2,
			'r-line-position' => array(1, 3)
		),
		array(
			'name' => 'Capitale',
			'desc' => 'Une capitale est chère, mais son efficacité, tant commerciale que militaire, vous fera vous développer considérablement plus vite. Vous ne pouvez créer qu’une capitale pour votre empire, néanmoins vous pouvez conquérir celles des joueurs ennemis pour en disposer de plusieurs.',

			'price' => 1000000,
			'school-size' => 8,

			'tax' => 1.25,
			'l-line' => 3,
			'l-line-position' => array(1, 2, 3),
			'r-line' => 2,
			'r-line-position' => array(1, 3)
		)
	);
}