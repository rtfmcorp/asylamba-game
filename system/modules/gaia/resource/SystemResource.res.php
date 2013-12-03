<?php
class SystemResource {
	public static function getInfo($id, $info) {
		if (in_array($info, array('id', 'frenchName'))) {
			return self::$systems[$id - 1][$info];
		} else {
			return FALSE;
		}
	}

	private static $systems = array(
		array(
			'id' => 1,
			'frenchName' => 'Cimetière Spatial',
		),
		array(
			'id' => 2,
			'frenchName' => 'Nébuleuse',
		),
		array(
			'id' => 3,
			'frenchName' => 'Géante Bleue',
		),
		array(
			'id' => 4,
			'frenchName' => 'Naine Jaune',
		),
		array(
			'id' => 5,
			'frenchName' => 'Naine Rouge',
		)
	);
}
?>