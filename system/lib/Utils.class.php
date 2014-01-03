<?php
class Utils {
	private static $autorizedChar = array(
		'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
	);

	public static function isAdmin($bindkey) {
		$bindkeys = array(
			'player1', 'player2', 'gil', 'noe', 'jacky',
			'jq2Bjf0uKzzE0aMErO6rpBxcg',
			'E6GatZvhO1W9seBHU7mgQe49c',
			'FaDcTV3lWokXHZO8tXH4divWZ',
			'KD6wb29ElI6sxVVtVoLJY0BbO',
			'xQTjMBnqbk6rO4ysglCJxLL63'
		);
		if(in_array($bindkey, $bindkeys)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function now() {
		return date('Y-m-d H:i:s');
	}

	public static function interval($date1, $date2, $precision = 'h') {
		if ($precision == 'h') {
			$date1  = explode(' ', $date1);
			$hour1 = explode(':', $date1[1]);
			$newDate1 = $date1[0] . ' ' . $hour1[0] . ':00:00';
			$time1 = strtotime($newDate1) / 3600;

			$date2  = explode(' ', $date2);
			$hour2 = explode(':', $date2[1]);
			$newDate2 = $date2[0] . ' ' . $hour2[0] . ':00:00';
			$time2 = strtotime($newDate2) / 3600;

			$interval = abs($time1 - $time2);
			return $interval;
		} elseif ($precision == 's') {
			$time1 = strtotime($date1);
			$time2 = strtotime($date2);

			$interval = abs($time1 - $time2);
			return $interval;
		}
	}

	public static function hasAlreadyHappened($date, $now = FALSE) {
		if ($now === FALSE) {
			$now = self::now();
		}

		if (strtotime($date) <= strtotime($now)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function addSecondsToDate($date, $seconds) {
		return date('Y-m-d H:i:s', strtotime($date) + $seconds);
	}

	public static function getDateFromTimestamp($timestamp) {
		return date('Y-m-d H:i:s', $timestamp);
	}

	public static function generateString($nbr) {
		$password = '';
		for ($i = 0; $i < $nbr; $i++) {
			$aleaChar = self::$autorizedChar[rand(0, count(self::$autorizedChar) - 1)];
			$password .= $aleaChar;
		}
		return $password;
	}

	public static function check($string, $mode = '') {
		$string = trim($string);
		$string = htmlspecialchars($string);
		if ($mode == 'complex') {
			$string = nl2br($string);
			$string = preg_replace('`http://[a-z0-9._,;/?!&=#-]+`i', '<a href="$0" target="blank">$0</a>', $string);
		}
		return $string;
	}
	
	public static function arrayToWhere($array, $prefix = '') {
		if (!empty($array)) {
			$i = 0;	$return = '';

			foreach ($array AS $k => $v) {
				if ($i == 0) {
					$return .= 'WHERE ';
				} else {
					$return .= ' AND ';
				}
				if (is_array($v)) {
					$return .= ' (';
					for ($j = 0; $j < count($v); $j++) { 
						if ($j == 0) {
							$return .= $prefix . $k . ' = ?';
						} else {
							$return .= ' OR ' . $prefix . $k . ' = ?';
						}
					}
					$return .= ') ';
				} else {
					$return .= $prefix . $k . ' = ? ';
				}
				$i++;
			}
			return $return;
		}
	}
	
	public static function arrayToOrder($array, $prefix = '') {
		if (!empty($array)) {
			$return = 'ORDER BY';
			$i = 0;
			foreach ($array AS $k) {
				if ($i % 2 != 1 AND $i != 0) {
					$return .= ',';
				}
				$return .= ' ' . $prefix . $k;
				$i++;
			}
			return $return;
		}
	}
	
	public static function arrayToLimit($array) {
		if (empty($array)) {
			return '';
		} else {
			return 'LIMIT ' . $array[0] . ', ' . $array[1];
		}
	}

	public static function hashAndSalt($string) {
		return sha1($string . 'abdelazer');
	}

	public static function getHTTPData($dataName) {
		if (CTR::$get->exist($dataName)) {
			return CTR::$get->get($dataName);
		} elseif (CTR::$post->exist($dataName)) {
			return CTR::$post->get($dataName);
		} else {
			return FALSE;
		}
	}
}