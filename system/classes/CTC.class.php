<?php
abstract class CTC {
	private static $running = FALSE;
	private static $currentDate = NULL;

	public static $events  = array();

	# debug variable
	public static $logme = '';

	public static function createContext($creator = NULL) {
		self::$create++;

		if (!self::$running) {
			self::$running = TRUE;
			self::$context++;
			self::$creator = $creator;

			self::$logme = '';

			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function applyContext($token) {
		self::$apply++;

		if ($token) {
			if (count(self::$events) > 0) {
				$beforeUsort = count(self::$events);
				
				/*usort(self::$events, function($a, $b) {
					if ($a['timest'] == $b['timest']) {
						return 0;
					} else {
						return ($a['timest'] < $b['timest'])
							? -1
							: 1;
					}
				});*/
				
				self::$events = CTC::insertion(self::$events);

				$afterUsort = count(self::$events);

				$logt  = '> ' . date('H:i:s') . ', start to apply context';
				$logt .= (CTR::$data->exist('playerId')) ? ' [Player ' . CTR::$data->get('playerId') . ']' : NULL;
				$logt .= "\n";

				$logt .= '> Page : ' . $_SERVER['REQUEST_URI'] . "\n";


				$j = 0;
				foreach (self::$events as $k => $event) {
					$j++;
					self::$currentDate = $event['date'];
					call_user_func_array(array($event['object'], $event['method']), $event['args']);
					
					$logt .= '> [' . $event['date'] . '] ' . get_class($event['object']) . '(' . $event['object']->getId() . ')::' . $event['method'] . "\n";
				}

				self::$running = FALSE;
				self::$events  = array();

				$logt .= '> ';
				$logt .= 'create/apply : ' . self::$create . '/' . self::$apply . ' | ';
				$logt .= 'add/iter : ' . self::$add . '/' . $j . ' | ';
				$logt .= 'before/after-usort : ' . $beforeUsort . '/' . $afterUsort . ' | ';
				$logt .= 'context : ' . self::$context . ' | ';
				$logt .= 'creator : ' . self::$creator . "\n";
				
				$logt .= '> logme : ' . self::$logme . "\n";

				$logt .= "> \n";

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
			self::$logme .= '(' . count(self::$events);
			$event = array(
				'timest' => strtotime($date), 
				'date' 	 => $date,
				'object' => $object,
				'method' => $method,
				'args'   => $args
			);
			self::$logme .= '|' . count(self::$events);
			self::$events[] = $event;
			self::$logme .= '|' . count(self::$events) . ') ';
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

	public static function insertion(array $array) {
        $length = count($array);

        for ($i = 1; $i < $length; $i++) {
            $element = $array[$i];
            $j = $i;

            while($j > 0 && $array[$j - 1]['timest'] > $element['timest']) {
                $array[$j] = $array[$j - 1];
                $j = $j - 1;
            }

            $array[$j] = $element;
        }

        return $array;
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