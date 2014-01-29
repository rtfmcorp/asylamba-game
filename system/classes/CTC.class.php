<?php
abstract class CTC {
	private static $running = FALSE;
	private static $events  = array();

	public static function createContext() {
		if (!self::$running) {
			self::$running = TRUE;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function applyContext($token) {
		if ($token) {
			foreach (self::$events as $k => $event) {
				call_user_func(array($event['object'], $event['method']), $event['args']);
			}

			self::$running = FALSE;
			self::$events  = array();
		}
	}

	public static function add($date, $object, $method, $args = array()) {
		if (!self::$running) {
			throw new Exception('CTC isn\'t running actually', 1);
		} else {
			$event = array(
				'date' 	 => $date,
				'object' => $object,
				'method' => $method,
				'args'   => $args
			);

			$index = 0;

			if (self::size() == 0) {
				self::$events[$index] = $event;
			} else {
				$found = FALSE;

				foreach(self::$events AS $e) {
					if (strtotime($e['date']) > strtotime($date)) {
						$found = TRUE;
						break;
					}
					$index++;
				}
				if ($found) {
					$begin			= array_slice(self::$events, 0, $index);
					$begin[]		= $event;
					$end			= array_slice(self::$events, $index);
					self::$events 	= array_merge($begin, $end);
				} else {
					self::$events[self::size()] = $event;
				}
			}
		}
	}

	public static function size() {
		return count(self::$events);
	}

	public static function log() {
		var_dump(self::$running);
		var_dump(self::$events);
		echo '----------------------------------';
	}
}
?>