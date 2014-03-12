<?php
class PlaceResource {
	public static function get($type, $info) {
		if (in_array($info, array('name', 'tax', 'l-line', 'r-line', 'l-line-position', 'r-line-position'))) {
			return self::$place[$type][$info];
		} else {
			return FALSE;
		}
	}

	private static $place = array(
		array(
			'name' => 'Colonie',
			'tax' => 1,
			'l-line' => 1,
			'l-line-position' => array(2),
			'r-line' => 1,
			'r-line-position' => array(2)
		),
		array(
			'name' => 'Centre Industriel',
			'tax' => 1.5,
			'l-line' => 1,
			'l-line-position' => array(2),
			'r-line' => 1,
			'r-line-position' => array(2)
		),
		array(
			'name' => 'Base Militaire',
			'tax' => 0.5,
			'l-line' => 3,
			'l-line-position' => array(1, 2, 3),
			'r-line' => 2,
			'r-line-position' => array(1, 3)
		),
		array(
			'name' => 'Overplanet',
			'tax' => 1.5,
			'l-line' => 3,
			'l-line-position' => array(1, 2, 3),
			'r-line' => 2,
			'r-line-position' => array(1, 3)
		)
	);
}
?>