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
	#	niv min, niv max, liste de vaisseaux

	#	extrèmement petit officier (lvl 1)
		array(1, 1, array(2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(1, 1, array(5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(1, 1, array(10, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),

	#	très petit officier (lvl 2-4)
		array(2, 2, array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 2, array(2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 2, array(4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 2, array(8, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 2, array(0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 3, array(0, 4, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 3, array(20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 3, array(25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 3, array(20, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 4, array(0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0)),
		array(2, 4, array(10, 10, 2, 2, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 6, array(10, 10, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(2, 6, array(0, 0, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0)),

	#	officier moyen (5-9)
	#	esquadrille moyenne chasseur
		array(5, 12, array(25, 10, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(5, 12, array(0, 25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(5, 12, array(0, 0, 6, 6, 1, 0, 0, 0, 0, 0, 0, 0)),
		array(5, 12, array(10, 10, 2, 2, 0, 0, 0, 0, 0, 0, 0, 0)),

	#	esquadrille moyenne gros
		array(5, 12, array(0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0)),
		array(5, 12, array(0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0)),
		array(5, 12, array(0, 0, 0, 0, 0, 0, 4, 0, 0, 0, 0, 0)),
		array(5, 12, array(0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0)),

	#	esquadrille moyenne mixte
		array(5, 12, array(0, 5, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0)),
		array(5, 12, array(12, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0)),

	#	officier fort (10-14)
	#	esquadrille forte chasseur
		array(10, 14, array(50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(10, 14, array(0, 33, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(10, 14, array(0, 0, 33, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(10, 15, array(0, 0, 0, 10, 10, 2, 0, 0, 0, 0, 0, 0)),
		array(10, 15, array(10, 10, 8, 2, 2, 2, 0, 0, 0, 0, 0, 0)),
		array(10, 15, array(10, 10, 8, 2, 2, 2, 0, 0, 0, 0, 0, 0)),
		array(10, 15, array(0, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, 0)),

	#	esquadrille forte gros
		array(10, 14, array(0, 0, 0, 0, 0, 0, 4, 0, 0, 0, 0, 0)),
		array(10, 14, array(0, 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0)),
		array(10, 14, array(0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0)),
		array(10, 14, array(0, 0, 0, 0, 0, 0, 2, 1, 0, 0, 0, 0)),
		array(10, 15, array(0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0)),
		array(10, 15, array(0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0)),
		array(10, 15, array(0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0)),
		array(10, 15, array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0)),

	#	esquadrille forte mixte
		array(10, 14, array(10, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0)),
		array(10, 14, array(5, 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0)),
		array(10, 14, array(0, 1, 1, 0, 0, 0, 2, 0, 0, 0, 0, 0)),
		array(10, 14, array(2, 0, 0, 0, 0, 0, 2, 1, 0, 0, 0, 0)),
		array(10, 15, array(0, 6, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0)),
		array(10, 15, array(5, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0)),
		array(10, 15, array(3, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0)),
		array(10, 15, array(2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0)),

	#	extrèmement fort officier (lvl 15)
		array(15, 15, array(2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1)),
	);
}
?>