<?php

namespace Asylamba\Classes\Library;

use Asylamba\Classes\Worker\CTR;

class Format {

	/*
	 * retourne un s ou un mot au pluriel si $number est supérieur à 1
	 * arg : $number
	 *     : int => nombre qui définit ou non le pluriel
	 * arg : $return *
	 *     : str => retourne la chaine (ou s si non spécifié) si number est supérieur à 1
	 * arg :
	 */
	public static function addPlural($number, $return = '', $expression = '') {
		if ($number > 1) {
			return ($expression == '' AND $return == '')
				? 's'
				: $return;
		} else {
			return ($expression == '')
				? NULL
				: $expression;
		}
	}

	public static function ordinalNumber($nbr) {
		switch ($nbr) {
			case 1:
				return 'premier';
			case 2:
				return 'deuxième';
			case 3:
				return 'troisième';
			case 4:
				return 'quatrième';
			case 5:
				return 'cinquième';
			case 6:
				return 'sixième';
			case 7:
				return 'septième';
			default:
				return $nbr . 'ème';
		}
	}

	public static function plural($number, $return = '', $expression = '') {
		return self::addPlural($number, $return, $expression);
	}

	/*
	 * retourne un nombre formté
	 * - en mettant des espaces chaque milliers
	 * - en choisissant le nombre de chiffre après la virgule
	 * arg : $number
	 *     : int => nombre à formater
	 * arg : $decimal *
	 *     : int => nombre de chiffre après la virgule
	 */
	public static function numberFormat($number, $decimals = 0) {
		return self::number($number, $decimals);
	}

	public static function number($number, $decimals = 0) {
		if ($decimals == -2 AND $number > 999999) {
			return number_format(ceil($number / 1000000), $decimals, ',', ' ') . ' Mio';
		} elseif ($decimals <= -1 AND $number > 9999) {
			return number_format(ceil($number / 1000), $decimals, ',', ' ') . ' k';
		} else {
			return number_format($number, $decimals, ',', ' ');
		}
	}

	public static function percent($number, $base, $ceil = TRUE) {
		return ($base == 0)
			? 0
			: ($ceil
				? ceil(($number / $base) * 100)
				: ($number / $base) * 100
			);
	}

	public static function rankingFormat($number) {
		if ($number == 1) {
			return '1er';
		} else {
			return $number . 'ème';
		}
	}

	public static function actionBuilder($action, $token, $params = array(), $hasRoot = TRUE) {
		$url = '';
		if ($hasRoot) {
			$url .= APP_ROOT;
		}
		$url .= 'action/';
		$url .= 'a-' . $action . '/';

		foreach ($params as $key => $value) {
			$url .= $key . '-' . $value . '/';
		}
		$url .= 'token-' . $token;
		return $url;
	}

	public static function paddingNumber($number, $size) {
		$digits = strlen((string) $number);

		if ($digits < $size) {
			for ($i = 0; $i < $size - $digits; $i++) {
				$number = '0' . $number;
			}
		}

		return $number;
	}
}
