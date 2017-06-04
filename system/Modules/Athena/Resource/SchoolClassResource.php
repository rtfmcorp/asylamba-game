<?php
namespace Asylamba\Modules\Athena\Resource;

use Asylamba\Classes\Exception\ErrorException;

class SchoolClassResource {
	private static $classes = array(
		array(
			'credit'  => 2500,
			'minSize' => 1,
			'maxSize' => 1,
			'minExp'  => 100,
			'maxExp'  => 210,
			'point'   => 5,
			'title'   => 'Engager un officier nul'),
		array(
			'credit'  => 2500,
			'minSize' => 1,
			'maxSize' => 1,
			'minExp'  => 100,
			'maxExp'  => 210,
			'point'   => 5,
			'title'   => 'Engager un officier nul'),
		array(
			'credit'  => 2500,
			'minSize' => 1,
			'maxSize' => 1,
			'minExp'  => 100,
			'maxExp'  => 210,
			'point'   => 5,
			'title'   => 'Engager un officier nul')
		);

	public static function getInfo($i, $info) {
		if (in_array($info, array('credit', 'minSize', 'maxSize', 'minExp', 'maxExp', 'point', 'title'))) {
			if ($i < self::size()) {
				return self::$classes[$i][$info];
			} else {
				return FALSE;
			}
		} else {
			throw new ErrorException('info inconnue dans getInfo de SchoolClassResource');
		}
	}

	public static function size() {
		return count(self::$classes);
	}
}