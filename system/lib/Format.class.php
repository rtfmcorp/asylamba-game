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
			return ($expression == '' AND $return == '') ? 's' : $return;
		} else {
			if ($expression == '') {
				return '';
			} else {
				return $expression;
			}
		}
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
		if ($decimals == -1 AND $number > 9999) {
			return ceil($number / 1000) . ' k';
		} else {
			return number_format($number, $decimals, ',', ' ');
		}
	}

	public static function percent($number, $base) {
		return ($base == 0) ? 0 : ceil(($number / $base) * 100);
	}
}