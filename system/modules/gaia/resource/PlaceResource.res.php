<?php
class PlaceResource {
	public static function get($type, $info) {
		if (in_array($info, array('name', 'tax', 'l-line', 'r-line'))) {
			return self::$place[$type][$info];
		} else {
			return FALSE;
		}
	}

	private static $place = array(
		array(
			'name' => 'Colonie',
			'tax' => 100,
			'l-line' => 1,
			'r-line' => 1
		),
		array(
			'name' => 'Centre Industriel',
			'tax' => 150,
			'l-line' => 1,
			'r-line' => 1
		),
		array(
			'name' => 'Base Militaire',
			'tax' => 50,
			'l-line' => 3,
			'r-line' => 2
		),
		array(
			'name' => 'Overplanet',
			'tax' => 150,
			'l-line' => 3,
			'r-line' => 2
		)
	);
}
?>