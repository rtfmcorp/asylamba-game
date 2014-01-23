<?php
abstract class GalaxyGenerator {
	# stats
	public static $nbSystem = 0;
	public static $listSystem = array();

	public static $nbPlace = 0;
	public static $popTotal = 0;
	public static $listPlace = array();

	public static $nbSector = 0;
	public static $systemDeleted = 0;
	public static $listSector = array();

	public static function generate() {
		# generation
		self::generateSystem();
		self::generatePlace();
		self::generateSector();
		self::associateSystemToSector();
		self::getStatisticsSector();
		
		# saving
		# self::clear();
		self::save();

		echo '- - - nbSystem : ' . Format::numberFormat(self::$nbSystem, 2) . '<br />';
		echo '- - - nbPlace : ' . Format::numberFormat(self::$nbPlace, 2) . '<br />';
		echo '- - - popTotal : ' . Format::numberFormat(self::$popTotal, 2) . '<br />';
		echo '- - - nbSector : ' . Format::numberFormat(self::$nbSector, 2) . '<br />';
		echo '- - - systemDeleted : ' . Format::numberFormat(self::$systemDeleted, 2) . '<br />';
	}

	public static function clear() {
		$db = DataBase::getInstance();
		$db->query('DELETE FROM place');
		$db->query('DELETE FROM system');
		$db->query('DELETE FROM sector');
	}

	public static function save() {
		/*$db = DataBase::getInstance();
		
		$qr = 'INSERT INTO place(id, rPlayer, rSystem, typeOfPlace, position, population, coefHistory, coefResources) VALUES ';
		foreach (self::$listPlace as $v) { $qr .= '(' . implode(', ', $v) . '), '; }
		$qr = substr($qr, 0, -2);
		$db->query($qr);*/	
	}

	private static function generateSystem() {
		# id
		$k = 1;

		for ($i = 1; $i <= GalaxyConfiguration::$galaxy['size']; $i++) {
			for ($j = 1; $j <= GalaxyConfiguration::$galaxy['size']; $j++) {
				# current cursor position
				$xPosition = $j;
				$yPosition = $i;
				
				# calcul de la distance entre la case et le centre
				$d2o = sqrt(
					pow(abs((GalaxyConfiguration::$galaxy['size'] / 2) - $xPosition), 2) + 
					pow(abs((GalaxyConfiguration::$galaxy['size'] / 2) - $yPosition), 2)
				);
				
				if (self::isPointInMap($d2o)) {
					$type = self::getSystem();

					self::$nbSystem++;
					self::$listSystem[] = array($k, 0, 0, $xPosition, $yPosition, $type);

					$k++;
				}
			}
		}
	}

	public static function generatePlace() {
		$k = 1;

		foreach (self::$listSystem AS $system) {
			$place = self::getNbOfPlace($system[5]);

			for ($i = 0; $i < $place; $i++) {
				$type 		= self::getTypeOfPlace($system[5]);
				$population = self::getPopulation($type);
				$history 	= self::getHistory($type);
				$resources 	= self::getResources($type);

				self::$nbPlace++;
				self::$popTotal += $population;
				self::$listPlace[] = array($k, 0, $system[0], $type, ($i + 1), $population, $history, $resources);
				$k++;
			}
		}
	}

	public static function generateSector() {
		$k = 1;

		foreach (GalaxyConfiguration::$sectors as $sector) {
			self::$nbSector++;
			self::$listSector[] = array(
				$k, 
				$sector['beginColor'], 
				$sector['display'][0], 
				$sector['display'][1], 
				$sector['barycentre'][0], 
				$sector['barycentre'][1], 
				5, 
				0, 0, 0, 0, 0, 0);

			$k++;
		}
	}

	public static function associateSystemToSector() {
		$pl = new PointLocation();
		$systemToDelete = array();
		$k = 0;

		foreach (self::$listSystem as $v) {
			foreach (GalaxyConfiguration::$sectors as $w) {
				$place = $pl->pointInPolygon($v[3] . ', ' . $v[4], $w['vertices']);

				if ($place == 1 OR $place == 2) {
					$systemToDelete[] = $v[0];
					break;
				} elseif ($place == 3) {
					self::$listSystem[$k][1] = $w['id'];
					break;
				}
			}
			$k++;
		}
		
		# suppression des systemes sur des lignes ou des angles
		for ($i = count(self::$listSystem) - 1; $i >= 0; $i--) { 
			if (in_array(self::$listSystem[$i][0], $systemToDelete)) {
				unset(self::$listSystem[$i]);
			}
		}

		# suppression des places liée a des systems supprimés
		for ($i = count(self::$listPlace) - 1; $i >= 0; $i--) { 
			if (in_array(self::$listPlace[$i][2], $systemToDelete)) {
				unset(self::$listPlace[$i]);
			}
		}

		self::$systemDeleted = count($systemToDelete);
	}

	private static function getStatisticsSector() {
		foreach (self::$listSector as $sector) {
			foreach (self::$listSystem as $system) {
				/***/
			}
		}
	}

	private static function isPointInMap($d2o) {
		$mask = rand(1, GalaxyConfiguration::$galaxy['mask']);

		if ($mask < 3) {
			$random = rand(0, 100);
			
			if ($d2o > 40) {
				$dToCircle = abs($d2o - 80);
				if ($d2o < 1) {$dToCircle = 1;}
				if ($dToCircle < 2) {
					if ($random < 100) { return TRUE; }
				} elseif ($dToCircle < 5) {
					if ($random < 95)  { return TRUE; }
				} elseif ($dToCircle < 10) {
					if ($random < 80)  { return TRUE; }
				} elseif ($dToCircle < 20) {
					if ($random < 40)  { return TRUE; }
				} elseif ($dToCircle < 35) {
					if ($random < 20)  { return TRUE; }
				} elseif ($dToCircle < 50) {
					if ($random < 7)   { return TRUE; }
				} else {
					if ($random < 1)   { return TRUE; }
				}
			} else {
				if ($d2o < 1) { $d2o = 1; }
				if ($d2o < 15) {
					if ($random < 100) { return TRUE; }
				} elseif ($d2o < 20) {
					if ($random < 95)  { return TRUE; }
				} elseif ($d2o < 27) {
					if ($random < 80)  { return TRUE; }
				} elseif ($d2o < 30) {
					if ($random < 50)  { return TRUE; }
				} elseif ($d2o < 33) {
					if ($random < 40)  { return TRUE; }
				} elseif ($d2o < 36) {
					if ($random < 50)  { return TRUE; }
				} elseif ($d2o < 38) {
					if ($random < 50)  { return TRUE; }
				} else {
					if ($random < 10)  { return TRUE; }
				}
			}
			return FALSE;
		} else {
			return FALSE;
		}
	}

	private static function getProportion($params, $value) {
		$cursor	= 0;
		$type 	= 0;
		$min 	= 0;
		$max 	= 0;

		for ($i = 0; $i < count($params); $i++) {
			if ($i == 0) {
				$max = $params[$i];
			} elseif ($i < count($params) - 1) {
				$min = $cursor;
				$max = $cursor + $params[$i];
			} else {
				$min = $cursor;
				$max = 100;
			}

			$cursor = $max;
			$type += 1;


			if ($value > $min && $value <= $max) {
				return $type;
			}
		}
	}

	private static function getSystem() {
		return self::getProportion(GalaxyConfiguration::$galaxy['systemProportion'], rand(1, 100));
	}

	private static function getNbOfPlace($systemType) {
		return rand(
			GalaxyConfiguration::$systems[$systemType - 1]['nbrPlaces'][0],
			GalaxyConfiguration::$systems[$systemType - 1]['nbrPlaces'][1]
		);
	}

	private static function getTypeOfPlace($systemType) {
		return self::getProportion(GalaxyConfiguration::$systems[$systemType - 1]['placesPropotion'], rand(1, 100));
	}

	private static function getHistory($placeType) {
		return rand(
			GalaxyConfiguration::$places[$placeType - 1]['history'][0],
			GalaxyConfiguration::$places[$placeType - 1]['history'][1]
		);
	}

	private static function getResources($placeType) {
		return rand(
			GalaxyConfiguration::$places[$placeType - 1]['resources'][0],
			GalaxyConfiguration::$places[$placeType - 1]['resources'][1]
		);
	}

	private static function getPopulation($placeType) {
		return ($placeType == 1)
			? (rand(
				GalaxyConfiguration::$galaxy['population'][0], 
				GalaxyConfiguration::$galaxy['population'][1]
			) / 100) 
			: 0;
	}
}
?>