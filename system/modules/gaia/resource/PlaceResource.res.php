<?php
class PlaceResource {
	public static function get($type, $info) {
		if (in_array($info, array('name', 'desc', 'price', 'tax', 'l-line', 'r-line', 'l-line-position', 'r-line-position'))) {
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

			'tax' => 1,
			'l-line' => 1,
			'l-line-position' => array(2),
			'r-line' => 1,
			'r-line-position' => array(2)
		),
		array(
			'name' => 'Centre Industriel',
			'desc' => '',

			'price' => 250000,

			'tax' => 1.5,
			'l-line' => 1,
			'l-line-position' => array(2),
			'r-line' => 1,
			'r-line-position' => array(2)
		),
		array(
			'name' => 'Base Militaire',
			'desc' => 'Place forte militaire, cette planète dispose d\'un nombre important de flottes en orbite, prêtes à défendre ou à attaquer.',

			'price' => 250000,

			'tax' => 0.5,
			'l-line' => 3,
			'l-line-position' => array(1, 2, 3),
			'r-line' => 2,
			'r-line-position' => array(1, 3)
		),
		array(
			'name' => 'Capitale',
			'desc' => '',

			'price' => 5000000,

			'tax' => 1.5,
			'l-line' => 3,
			'l-line-position' => array(1, 2, 3),
			'r-line' => 2,
			'r-line-position' => array(1, 3)
		)
	);
}
?>