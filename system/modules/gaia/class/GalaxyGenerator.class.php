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

	public static $output;

	public static function generate() {
		# generation
		self::generateSystem();
		self::generatePlace();
		self::generateSector();
		self::associateSystemToSector();

		self::save();

		self::getStatisticsSector();
	}

	public static function clear() {
		$db = DataBaseAdmin::getInstance();

		$db->query('TRUNCATE place');
		self::log('table `place` vidées');

		$db->query('TRUNCATE system');
		self::log('table `system` vidées');

		$db->query('TRUNCATE sector');
		self::log('table `sector` vidées');

		self::log('_ _ _ _');
	}

	public static function save() {
		$db = DataBaseAdmin::getInstance();

		# clean up database
		self::clear();

		self::log('sauvegarde des places');
		for ($i = 0; $i < ceil(count(self::$listPlace) / 5000); $i++) { 
			$qr = 'INSERT INTO place(id, rPlayer, rSystem, typeOfPlace, position, population, coefResources, coefHistory, resources, uPlace) VALUES ';
			
			for ($j = $i * 5000; $j < (($i + 1) * 5000) - 1; $j++) { 
				if (isset(self::$listPlace[$j])) {
					$qr .= '(' . implode(', ', self::$listPlace[$j]) . ', NOW()), ';
				}
			}

			$qr = substr($qr, 0, -2);
			$db->query($qr);
		}
		self::log(ceil(count(self::$listPlace) / 5000) . ' requêtes `INSERT`');

		self::log('sauvegarde des systèmes');
		for ($i = 0; $i < ceil(count(self::$listSystem) / 5000); $i++) { 
			$qr = 'INSERT INTO system(id, rSector, rColor, xPosition, yPosition, typeOfSystem) VALUES ';
			
			for ($j = $i * 5000; $j < (($i + 1) * 5000) - 1; $j++) { 
				if (isset(self::$listSystem[$j])) {
					$qr .= '(' . implode(', ', self::$listSystem[$j]) . '), ';
				}
			}

			$qr = substr($qr, 0, -2);
			$db->query($qr);
		}
		self::log(ceil(count(self::$listSystem) / 5000) . ' requêtes `INSERT`');

		self::log('sauvegarde des secteurs');
		for ($i = 0; $i < ceil(count(self::$listSector) / 5000); $i++) { 
			$qr = 'INSERT INTO sector(id, rColor, xPosition, yPosition, xBarycentric, yBarycentric, tax, population, lifePlanet, name, prime) VALUES ';
			
			for ($j = $i * 5000; $j < (($i + 1) * 5000) - 1; $j++) { 
				if (isset(self::$listSector[$j])) {
					$qr .= '(\'' . implode('\', \'', self::$listSector[$j]) . '\'), ';
				}
			}

			$qr = substr($qr, 0, -2);
			$db->query($qr);
		}
		self::log(ceil(count(self::$listSector) / 5000) . ' requêtes `INSERT`');

		self::log('_ _ _ _');
	}

	public static function getLog() {
		$rt  = '<pre style="font-family: consolas;">';
			$rt .= self::$output;
		$rt .= '</pre>';

		return $rt;
	}

	private static function log($text) {
		self::$output = self::$output . ">_ " . $text . "<br />";
	}

	private static function generateSystem() {
		self::log('génération des systèmes');
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

		self::log(self::$nbSystem . ' systèmes générés');
		self::log('_ _ _ _');
	}

	public static function generatePlace() {
		self::log('génération des places');
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
				self::$listPlace[] = array($k, 0, $system[0], $type, ($i + 1), $population, $resources, $history, 0);
				$k++;
			}
		}

		self::log(self::$nbPlace . ' places générées');
		self::log(Format::numberFormat(self::$popTotal * 1000000) . ' de population');
		self::log('_ _ _ _');
	}

	public static function generateSector() {
		self::log('génération des secteurs');
		$k = 1;

		foreach (GalaxyConfiguration::$sectors as $sector) {
			self::$nbSector++;

			$prime = ($sector['beginColor'] != 0)
				? 1
				: 0;

			self::$listSector[] = array(
				$k, 
				$sector['beginColor'], 
				$sector['display'][0], 
				$sector['display'][1], 
				$sector['barycentre'][0], 
				$sector['barycentre'][1], 
				5, 
				0,
				0,
				$sector['name'],
				$prime
			);

			$k++;
		}

		self::log(self::$nbSector . ' secteurs générés');
		self::log('_ _ _ _');
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
		$db = DataBaseAdmin::getInstance();

		foreach (self::$listSector as $sector) {
			$id = $sector[0];

			$qr = $db->prepare('SELECT
					COUNT(pl.id) AS planet,
					SUM(pl.population) AS population
				FROM sector AS se
				LEFT JOIN system AS sy
					ON se.id = sy.rSector
				LEFT JOIN place AS pl
					ON sy.id = pl.rSystem
				WHERE pl.typeOfPlace = 1
				AND se.id = ?');
			$qr->execute(array($id));
			$aw = $qr->fetch();

			$nbrPlanet = $aw['planet'];
			$population = ceil($aw['population']);

			$qr->closeCursor();

			$qr = $db->prepare('UPDATE sector SET lifePlanet = ?, population = ? WHERE id = ?');
			$qr->execute(array($nbrPlanet, $population, $id));

			$qr->closeCursor();
		}
	}

	private static function isPointInMap($d2o) {
		$mask = rand(1, GalaxyConfiguration::$galaxy['mask']);

		if ($mask < 3) {
			$realPosition = GalaxyConfiguration::$galaxy['diag'] - $d2o;
			$step 		  = GalaxyConfiguration::$galaxy['diag'] / count(GalaxyConfiguration::$galaxy['systemPosition']);
			$currentStep  = floor($realPosition / $step);

			$random = rand(0, 100);

			if (GalaxyConfiguration::$galaxy['systemPosition'][$currentStep] > $random) {
				return TRUE;
			} else {
				return FALSE;
			}
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