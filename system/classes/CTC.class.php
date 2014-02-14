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

		if ($token AND count(self::$events) > 0) {
			Bug::writeLog($path, '> ' . date('H:i:s') . ', start to apply context');
			Bug::writeLog($path, '');
			
			foreach (self::$events as $k => $event) {
				call_user_func_array(array($event['object'], $event['method']), $event['args']);

				Bug::writeLog($path, '> [' . $event['date'] . '] ' . get_class($event['object']) . '(' . $event['object']->getId() . ')::' . $event['method']);
			}

			Bug::writeLog($path, '');
			Bug::writeLog($path, '> ' . date('H:i:s') . ', end of apply context');
			Bug::writeLog($path, '');
			Bug::writeLog($path, '');
			Bug::writeLog($path, '');

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

			$timestamp = strtotime($date);
			$events = array();

			if (self::size() == 0) {
				self::$events[] = $event;
			} else {
				foreach(self::$events AS $e) {
					if (strtotime($e['date']) > $timestamp) {
						$events[] = $event;
						$events[] = $e;
					} else {
						$events[] = $e;
					}
				}

				self::$events = $events;
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