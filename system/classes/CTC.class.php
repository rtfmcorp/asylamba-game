<?php
abstract class CTC {
	private static $running = FALSE;
	private static $currentDate = NULL;

	public static $events  = array();

	public static function createContext($creator = NULL) {
		self::$create++;

		if (!self::$running) {
			self::$running = TRUE;
			self::$context++;
			self::$creator = $creator;

			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function applyContext($token) {
		self::$apply++;

		if ($token) {
			if (count(self::$events) > 0) {
				usort(self::$events, function($a, $b) {
					return $a['timest'] < $b['timest'] ? -1 : 1;
				});

				$logt  = '> ' . date('H:i:s') . ', start to apply context';
				$logt .= (CTR::$data->exist('playerId')) ? ' [Player ' . CTR::$data->get('playerId') . ']' : NULL;
				$logt .= "\n";
				
				foreach (self::$events as $k => $event) {
					self::$currentDate = $event['date'];
					call_user_func_array(array($event['object'], $event['method']), $event['args']);
					
					$logt .= '> [' . $event['date'] . '] ' . get_class($event['object']) . '(' . $event['object']->getId() . ')::' . $event['method'] . "\n";
				}

				self::$running = FALSE;
				self::$events  = array();

				$logt .= '> ' . date('H:i:s') . ', end of apply context' . "\n";
				$logt .= '> Stat | ';
				$logt .= 'create/apply : ' . self::$create . '/' . self::$apply . ' | context : ' . self::$context . ' | add ' . self::$add;
				$logt .= ' | creator : ' . self::$creator . "\n";
				$logt .= "\n";
				
				$path  = 'public/log/ctc/' . date('Y') . '-' . date('m') . '-' . date('d') . '.log';
				Bug::writeLog($path, $logt);
			} else {
				self::$running = FALSE;
			}
		}
	}

	public static function add($date, $object, $method, $args = array()) {
		if (!self::$running) {
			throw new Exception('CTC isn\'t running actually', 1);
		} else {
			self::$add++;

			$event = array(
				'timest' => strtotime($date), 
				'date' 	 => $date,
				'object' => $object,
				'method' => $method,
				'args'   => $args
			);

			self::$events[] = $event;
		}
	}

	public static function now() {
		if (self::$running) {
			return self::$currentDate;
		} else {
			return Utils::now();
		}
	}

	public static function size() {
		return count(self::$events);
	}

	private static $add     = 0;
	private static $create  = 0;
	private static $apply   = 0;
	private static $context = 0;
	private static $creator = NULL;

	public static function get() {
		return self::$events;
	}
}
?>