<?php
class Params {
	const LIST_ALL_FLEET = 1;

	private static $params = [
		self::LIST_ALL_FLEET => TRUE
	];

	public static function check($params) {
		return CTR::$cookie->exist('p' . $params)
			? (bool)CTR::$cookie->get('p' . $params)
			: self::$params[$params];
	}

	public static function update($params, $value) {
		if (in_array($params, self::$params)) {
			CTR::$cookie->add('p' . $params, $value);
		}
	}

	public static function getParams() {
		return self::$params;
	}
}
?>