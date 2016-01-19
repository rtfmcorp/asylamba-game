<?php
class Chronos {
	const SN_STR	= 'STR';
	const LN_STR	= 'strate';
	const CO_STR	= 2400;
	const SN_SEG	= 'SEG';
	const LN_SEG	= 'segment';
	const CO_SEG	= 24;
	const SN_REL	= 'REL';
	const LN_REL	= 'relève';

	const SN_MIN	= '\'';
	const LN_MIN	= 'minute';
	const SN_SEC	= '\'\'';
	const LN_SEC	= 'seconde';

	const REAL_TIME = SERVER_START_TIME;
	const SEG_SHIFT = 430;
	
	/*
	 * retourne le temps restant avant la prochaine relève
	 * arg : $type
	 *     : str 'i' => minutes
	 *     : str 's' => secondes
	 */
	public static function getTimer($type) {
		$ret = 60 - (date($type) + 1);
		if ($ret < 10) {
			$ret = '0' . $ret;
		}
		return (int) $ret;
	}

	/*
	 * retourne le temps écoulé depuis le début du serveur
	 * arg : $type
	 *     : str 'str' => strates
	 *     : str 'seg' => segments
	 *     : str 'rel' => relèves
	 *     : str 'min' => minutes
	 *     : str 'sec' => secondes
	 */
	public static function getDate($type) {
		$now  = time();
		$date = strtotime(Chronos::REAL_TIME);
		$intr = $now - $date;
		$rel  = (floor($intr / 3600)) + (Chronos::SEG_SHIFT * Chronos::CO_SEG);
		
		if ($type == 'str') {
			return floor($rel / Chronos::CO_STR);
		} elseif ($type == 'seg') {
			return floor($rel / Chronos::CO_SEG);
		} elseif ($type == 'rel') {
			$str = floor($rel / Chronos::CO_STR);
			$seg = floor(($rel - ($str * Chronos::CO_STR)) / Chronos::CO_SEG);
			return $rel - ($str * Chronos::CO_STR) - ($seg * Chronos::CO_SEG) + 1;
		}
	}

	private static function getRel($date) {
		$origin = strtotime(Chronos::REAL_TIME);
		$date 	= strtotime($date);
		$intr 	= $date - $origin;

		return (floor($intr / 3600)) + (Chronos::SEG_SHIFT * Chronos::CO_SEG);
	}

	/*
	 * transforme une date en temps de jeu
	 * arg : $date
	 *     : str => date au format sql (2012-08-01 18:30:00)
	 * arg : $reference *
	 *     : bol TRUE  => date puis le début de serveur
	 *     : bol FALSE => date par rapport à NOW()
	 * arg : $collapse *
	 *     : bol TRUE  => la date retournée est à la seconde près
	 *     : bol FALSE => la date est collée à la relève précédente
	 */
	public static function transform($date, $reference = FALSE, $collapse = FALSE) {
		$date = new DateTime($date);

		$releve  = self::getRel($date->format('Y-m-d H:i:s'));
		$segment = floor($releve / Chronos::CO_SEG);
		$releve -= $segment * Chronos::CO_SEG;

		$return = 'SEG' . $segment . ' REL' . $releve;
		$title  = $date->format('j.m.Y à H:i:s');

		return '<span class="hb lt" title="' . $title . '">' . $return . '</span>';
	}

	public static function secondToFormat($seconds, $format = 'large') {
		$return = '';
		$rel = floor($seconds / 3600);
		$min = floor(($seconds - ($rel * 3600)) / 60);
		$sec = $seconds - ($rel * 3600) - ($min * 60);

		if ($format == 'large') {
			$return .= ($rel > 0) ? $rel . ' ' . Chronos::LN_REL . Format::addPlural($rel) . ', ' : '';
			$return .= ($min > 0) ? $min . ' ' . Chronos::LN_MIN . Format::addPlural($min) . ', ' : '';
			$return .= ($sec > 0) ? $sec . ' ' . Chronos::SN_SEC : '';
		} elseif ($format == 'short') {
			$return .= $rel . ' ' . Chronos::SN_REL . Format::addPlural($rel) . ', ' . $min . ' ' . Chronos::SN_MIN . ', ' . $sec . ' ' . Chronos::SN_SEC;
		} elseif ($format == 'lite') {
			$min = ($min > 9) ? $min : '0' . $min;
			$sec = ($sec > 9) ? $sec : '0' . $sec;
			$return .= $rel . ':' . $min . ':' . $sec;
		}

		return trim($return, ', ');
	}
}