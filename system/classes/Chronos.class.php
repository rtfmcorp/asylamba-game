<?php
class Chronos {
	private static $strateShortName  = 'strate';
	private static $strateLongName   = 'strate';
	private static $strateCoeff		 = 720;
	private static $segmentShortName = 'segment';
	private static $segmentLongName  = 'segment';
	private static $segmentCoeff 	 = 24;
	private static $releveShortName  = 'relève';
	private static $releveLongName   = 'relève';
	private static $minuteShortName  = '\'';
	private static $minuteLongName   = 'minute';
	private static $secondShortName  = '\'\'';
	private static $secondLongName   = 'seconde';

	const REALTIME = '2014-01-10 18:00:00';
	const PLAYTIME = '16604';
	
	/*
	 * return le temps restant avant la prochaine relève
	 * arg : $type
	 *     : str 'i' => minutes
	 *     : str 's' => secondes
	 */
	public static function getTimer($type) {
		$ret = 60 - (date($type) + 1);
		if ($ret < 10) {
			$ret = '0' . $ret;
		}
		return $ret;
	}

	/*
	 * return le temps écouler depuis le début du serveur
	 * arg : $type
	 *     : str 'str' => strates
	 *     : str 'seg' => segments
	 *     : str 'rel' => relèves
	 *     : str 'min' => minutes
	 *     : str 'sec' => secondes
	 */
	public static function getDate($type) {
		$now  = time();
		$date = strtotime(Chronos::REALTIME);
		$itv  = abs($now - $date);
		$rel  = (floor($itv / 3600)) + Chronos::PLAYTIME;
		if ($type == 'str') {
			$str  = floor($rel / self::$strateCoeff);
			return $str;
		} elseif ($type == 'seg') {
			$str  = floor($rel / self::$strateCoeff);
			$seg  = floor(($rel - ($str * self::$strateCoeff)) / self::$segmentCoeff);
			return $seg;
		} elseif ($type == 'rel') {
			$str  = floor($rel / self::$strateCoeff);
			$seg  = floor(($rel - ($str * self::$strateCoeff)) / self::$segmentCoeff);
			$rel  = $rel - ($str * self::$strateCoeff) - ($seg * self::$segmentCoeff);
			return $rel + 1;
		}
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
		if (!empty($date)) {
			if ($reference) {
				$return = 'a faire !';
				// temps depuis
			} else {
				$return = '';
				
				// définition des composants nécessaires
				$now  = time();
				$date = strtotime($date);
				$dir  = ($now - $date >= 0) ? 'pasted' : 'futur';
				$itv  = abs($now - $date);

				// normalisation de l'interval
				$rel  = floor($itv / 3600);
				if ($collapse) { $rel++; }
				$itv -= $rel * 3600;
				$str  = floor($rel / self::$strateCoeff);
				$rel -= $str * self::$strateCoeff;
				$seg  = floor($rel / self::$segmentCoeff);
				$rel -= $seg * self::$segmentCoeff;
				if ($collapse) {
					$min = 0;
				} else {
					$min  = floor($itv / 60);
					$sec  = $itv - ($min * 60);
				}
				$data = array();
				if ($str > 0) { $data[self::$strateLongName] = $str; }
				if ($seg > 0) { $data[self::$segmentLongName] = $seg; }
				if ($rel > 0) { $data[self::$releveLongName] = $rel; }
				if ($min > 0) { $data[self::$minuteLongName] = $min; }

				$return = ($dir == 'pasted') ? 'il y a ' : 'dans ';

				if (!empty($data)) {
					$max = 1;
					foreach ($data AS $k => $v) {
						if ($max == 1) {
							$return .= $v . ' ' . $k . Format::addPlural($v);
						} elseif ($max == 2) {
							$return .= ' et ' . $v . ' ' . $k . Format::addPlural($v);
						} else {
							break;
						}
						$max++;
					}
				} else {
					$return .= 'moins d\'une minute';
				}
			}
			return $return;
		} else {
			return FALSE;
		}
	}

	public static function secondToFormat($seconds, $format = 'large') {
		$return = '';
		$rel = floor($seconds / 3600);
		$min = floor(($seconds - ($rel * 3600)) / 60);
		$sec = $seconds - ($rel * 3600) - ($min * 60);

		if ($format == 'large') {
			$return .= ($rel > 0) ? $rel . ' ' . self::$releveLongName . Format::addPlural($rel) . ', ' : '';
			$return .= ($min > 0) ? $min . ' ' . self::$minuteLongName . Format::addPlural($min) . ', ' : '';
			$return .= ($sec > 0) ? $sec . ' ' . self::$secondLongName . Format::addPlural($sec) : '';
		} elseif ($format == 'short') {
			$return .= $rel . ' ' . self::$releveShortName . Format::addPlural($rel) . ', ' . $min . ' ' . self::$minuteShortName . Format::addPlural($min) . ', ' . $sec . ' ' . self::$secondShortName . Format::addPlural($sec);
		} elseif ($format == 'lite') {
			$min = ($min > 9) ? $min : '0' . $min;
			$sec = ($sec > 9) ? $sec : '0' . $sec;
			$return .= $rel . ':' . $min . ':' . $sec;
		}

		return trim($return, ', ');
	}

	public static function convert($nbrReleve) {
		if (!empty($nbrReleve)) {

		} else {
			return FALSE;
		}
	}
}