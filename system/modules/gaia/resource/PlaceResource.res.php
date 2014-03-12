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
			'tax' => 1,
			'l-line' => 1,
			'r-line' => 1
		),
		array(
			'name' => 'Centre Industriel',
			'tax' => 1.5,
			'l-line' => 1,
			'r-line' => 1
		),
		array(
			'name' => 'Base Militaire',
			'tax' => 0.5,
			'l-line' => 3,
			'r-line' => 2
		),
		array(
			'name' => 'Overplanet',
			'tax' => 1.5,
			'l-line' => 3,
			'r-line' => 2
		)
	);
}
?>