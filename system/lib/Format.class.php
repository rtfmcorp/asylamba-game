<?php
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
			break;

			case 2:
				return 'deuxième';
			break;

			case 3:
				return 'troisième';
			break;

			case 4:
				return 'quatrième';
			break;

			case 5:
				return 'cinquième';
			break;

			case 6:
				return 'sixième';
			break;

			case 7:
				return 'septième';
			break;

			default:
				return $nbr . 'ème';
			break;

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
		return ($decimals == -1 AND $number > 9999)
			? number_format(ceil($number / 1000), $decimals, ',', ' ') . ' k'
			: number_format($number, $decimals, ',', ' ');
	}

	public static function number($number, $decimals = 0) {
		return self::numberFormat($number, $decimals);
	}

	public static function percent($number, $base, $ceil = TRUE) {
		return ($base == 0)
			? 0
			: ($ceil
				? ceil(($number / $base) * 100)
				: ($number / $base) * 100
			);
	}
}