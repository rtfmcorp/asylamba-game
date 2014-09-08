<?php
class SquadronResource {
	public static function get($level, $aleaNbr) {
		while ($aleaNbr >= self::size()) {
			$aleaNbr -=  self::size();
		}
		if ($aleaNbr < 0) {
			$aleNbr == 0;
		}

		$squadron = self::$squadrons[0][2];
		for ($i = 0; $i < self::size(); $i++, $aleaNbr++) {
			if ($aleaNbr >= self::size()) {
				$aleaNbr = 0;
			}

			if (self::$squadrons[$aleaNbr][0] <= $level && self::$squadrons[$aleaNbr][1] >= $level) {
				$squadron = self::$squadrons[$aleaNbr][2];
				break;
			}
		}

		$squadron[] = Utils::now();
		return $squadron;
	}

	public static function size() {
		return count(self::$squadrons);
	}

	private static $squadrons = array(
		array(1, 1, array(1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(1, 2, array(1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(1, 3, array(2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(1, 3, array(2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(1, 4, array(3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 3, array(3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 4, array(4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 4, array(3, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 5, array(1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(3, 4, array(2, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(3, 5, array(2, 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(3, 6, array(4, 3, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(4, 6, array(10, 5, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(4, 6, array(11, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(4, 7, array(12, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(5, 7, array(11, 0, 1, 0, 3, 2, 0, 0, 0, 0, 0, 0)),
		array(5, 8, array(1, 0, 0, 2, 0, 4, 0, 0, 0, 0, 0, 0)),
		array(6, 9, array(1, 7, 6, 3, 0, 3, 0, 0, 0, 0, 0, 0)),
		array(6, 10, array(1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0)),
		array(7, 10, array(1, 0, 3, 0, 0, 0, 1, 1, 0, 0, 0, 0)),
		array(7, 10, array(1, 0, 2, 0, 0, 2, 0, 1, 0, 0, 0, 0)),
		array(8, 11, array(1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(8, 11, array(1, 22, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0)),
		array(9, 11, array(1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0)),
		array(9, 12, array(1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0)),
		array(9, 12, array(2, 0, 2, 0, 0, 0, 8, 1, 0, 0, 0, 0)),
		array(9, 13, array(1, 0, 0, 0, 0, 0, 0, 2, 0, 1, 0, 0)),
		array(10, 13, array(1, 0, 0, 0, 4, 0, 1, 0, 0, 0, 0, 0)),
		array(10, 13, array(45, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(10, 14, array(1, 0, 0, 0, 0, 0, 0, 5, 0, 0, 0, 0)),
		array(11, 13, array(1, 0, 0, 0, 0, 0, 2, 3, 0, 0, 0, 0)),
		array(11, 14, array(1, 0, 10, 0, 0, 0, 1, 0, 1, 0, 0, 0)),
		array(11, 15, array(1, 2, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0)),
		array(12, 15, array(1, 0, 0, 0, 0, 8, 3, 0, 0, 0, 0, 0)),
		array(12, 15, array(1, 0, 0, 3, 2, 1, 4, 0, 0, 0, 0, 0)),
		array(13, 15, array(0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 1, 0)),
		array(14, 15, array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1))
	);
}
?>