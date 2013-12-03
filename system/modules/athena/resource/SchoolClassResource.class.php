<?php
class SchoolClassResource {
	private static $classes = array(
		array(
			'credit'  => 1500,
			'minSize' => 2,
			'maxSize' => 4,
			'minExp'  => 100,
			'maxExp'  => 210,
			'point'   => 25),
		array(
			'credit'  => 2500,
			'minSize' => 5,
			'maxSize' => 8,
			'minExp'  => 100,
			'maxExp'  => 210,
			'point'   => 60),
		array(
			'credit'  => 85000,
			'minSize' => 2,
			'maxSize' => 4,
			'minExp'  => 400,
			'maxExp'  => 1600,
			'point'   => 250),
		array(
			'credit'  => 150000,
			'minSize' => 5,
			'maxSize' => 8,
			'minExp'  => 400,
			'maxExp'  => 1600,
			'point'   => 600) 
		);

	public static function getInfo($size, $level, $info) {
		if (in_array($info, array('credit', 'minSize', 'maxSize', 'minExp', 'maxExp', 'point'))) {
			if ($size == 0) {
				if ($level == 0) {
					return self::$classes[0][$info];
				} elseif ($level == 1) {
					return self::$classes[2][$info];
				} else { return FALSE;}
			} elseif ($size == 1) {
				if ($level == 0) {
					return self::$classes[1][$info];
				} elseif ($level == 1) {
					return self::$classes[3][$info];
				} else { return FALSE;}
			} else { return FALSE; }
		} else {
			CTR::$alert->add('info inconnue dans getInfo de SchoolClassResource', ALT_BUG_ERROR);
			return FALSE;
		}
	}
}
?>