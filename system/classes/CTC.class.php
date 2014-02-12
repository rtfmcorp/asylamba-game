<?php
abstract class CTC {
	private static $running = FALSE;
	private static $events  = array();

	public static function createContext() {
		self::$create++;

		if (!self::$running) {
			self::$running = TRUE;
			self::$context++;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function applyContext($token) {
		self::$apply++;
		$path = 'public/log/ctc/' . date('Y') . '-' . date('m') . '-' . date('d') . '.log';

		if ($token) {
			Bug::writeLog($path, "> " . date('H:i:s') . ", start to apply context\r");
			Bug::writeLog($path, ">\r");
			
			foreach (self::$events as $k => $event) {
				call_user_func_array(array($event['object'], $event['method']), $event['args']);

				Bug::writeLog($path, "> " . get_class($event['object']) . "->" . $event['method'] . "(" . implode(', ', get_class($event['args'])) . ")\r");
			}

			self::$running = FALSE;
			self::$events  = array();
		}
	}

	public static function add($date, $object, $method, $args = array()) {
		if (!self::$running) {
			throw new Exception('CTC isn\'t running actually', 1);
		} else {
			self::$add++;

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

	private static $add     = 0;
	private static $create  = 0;
	private static $apply   = 0;
	private static $context = 0;
}
?>