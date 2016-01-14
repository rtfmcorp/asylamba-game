<?php
abstract class GalaxyGenerator {
	const MAX_QUERY = 5000;

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
		self::clear();

		# generation
		self::generateSector();
		self::generateSystem();
		self::associateSystemToSector();
		self::generatePlace();

		self::save();

		self::getStatisticsSector();
	}

	public static function clear() {
		$db = DataBaseAdmin::getInstance();

		$db->query('SET FOREIGN_KEY_CHECKS = 0;');
		
		$db->query('TRUNCATE place');
		self::log('table `place` vidées');

		$db->query('TRUNCATE system');
		self::log('table `system` vidées');

		$db->query('TRUNCATE sector');
		self::log('table `sector` vidées');

		$db->query('SET FOREIGN_KEY_CHECKS = 1;');
		self::log('_ _ _ _');
	}

	public static function save() {
		$db = DataBaseAdmin::getInstance();

		# clean up database
		self::clear();

		self::log('sauvegarde des secteurs');
		for ($i = 0; $i < ceil(count(self::$listSector) / GalaxyGenerator::MAX_QUERY); $i++) { 
			$qr = 'INSERT INTO sector(id, rColor, xPosition, yPosition, xBarycentric, yBarycentric, tax, population, lifePlanet, name, prime, points) VALUES ';
			
			for ($j = $i * GalaxyGenerator::MAX_QUERY; $j < (($i + 1) * GalaxyGenerator::MAX_QUERY) - 1; $j++) { 
				if (isset(self::$listSector[$j])) {
					$qr .= '(\'' . implode('\', \'', self::$listSector[$j]) . '\'), ';
				}
			}

			$qr = substr($qr, 0, -2);
			$db->query($qr);
		}
		self::log(ceil(count(self::$listSector) / GalaxyGenerator::MAX_QUERY) . ' requêtes `INSERT`');

		self::log('sauvegarde des systèmes');
		for ($i = 0; $i < ceil(count(self::$listSystem) / GalaxyGenerator::MAX_QUERY); $i++) { 
			$qr = 'INSERT INTO system(id, rSector, rColor, xPosition, yPosition, typeOfSystem) VALUES ';
			
			for ($j = $i * GalaxyGenerator::MAX_QUERY; $j < (($i + 1) * GalaxyGenerator::MAX_QUERY) - 1; $j++) { 
				if (isset(self::$listSystem[$j])) {
					$qr .= '(' . implode(', ', self::$listSystem[$j]) . '), ';
				}
			}

			$qr = substr($qr, 0, -2);
			$db->query($qr);
		}
		self::log(ceil(count(self::$listSystem) / GalaxyGenerator::MAX_QUERY) . ' requêtes `INSERT`');

		self::log('sauvegarde des places');
		for ($i = 0; $i < ceil(count(self::$listPlace) / GalaxyGenerator::MAX_QUERY); $i++) { 
			$qr = 'INSERT INTO place(id, rSystem, typeOfPlace, position, population, coefResources, coefHistory, resources, danger, maxDanger, uPlace) VALUES ';
			
			for ($j = $i * GalaxyGenerator::MAX_QUERY; $j < (($i + 1) * GalaxyGenerator::MAX_QUERY) - 1; $j++) { 
				if (isset(self::$listPlace[$j])) {
					$qr .= '(' . implode(', ', self::$listPlace[$j]) . ', "' . Utils::addSecondsToDate(Utils::now(), -259200) . '"), ';
				}
			}

			$qr = substr($qr, 0, -2);
			$db->query($qr);
		}
		self::log(ceil(count(self::$listPlace) / GalaxyGenerator::MAX_QUERY) . ' requêtes `INSERT`');

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

		# GENERATION DES LINES
		for ($w = 0; $w < count(GalaxyConfiguration::$galaxy['lineSystemPosition']); $w++) {
			# line point
			$xA = GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][0][0];
			$yA = GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][0][1];

			$xB = GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][1][0];
			$yB = GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][1][1];

			$l  = sqrt(pow($xB - $xA, 2) + pow($yB - $yA, 2));

			for ($i = 1; $i <= GalaxyConfiguration::$galaxy['size']; $i++) {
				for ($j = 1; $j <= GalaxyConfiguration::$galaxy['size']; $j++) {
					# current cursor position
					$xC = $j;
					$yC = $i;

					$d  = self::distToSegment($xC, $yC, $xA, $yA, $xB, $yB);

					$thickness = GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][2];
					$intensity = GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][3];

					if ($d < GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][2]) {
						#$prob = rand(0, GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][3]);
						$prob = rand(0, 100);


						#if (GalaxyConfiguration::$galaxy['lineSystemPosition'][$w][2] - $d > $prob) {
						if (round($intensity - ($d * $intensity / $thickness)) >= $prob) {


							$type = self::getSystem();

							self::$nbSystem++;
							self::$listSystem[] = array($k, 0, 0, $xC, $yC, $type);

							$k++;
						}
					}
				}
			}
		}

		# GENERATION DES ANNEAUX (circleSystemPosition)
		for ($w = 0; $w < count(GalaxyConfiguration::$galaxy['circleSystemPosition']); $w++) {
			# line point
			$xC = GalaxyConfiguration::$galaxy['circleSystemPosition'][$w][0][0];
			$yC = GalaxyConfiguration::$galaxy['circleSystemPosition'][$w][0][1];

			$radius 	= GalaxyConfiguration::$galaxy['circleSystemPosition'][$w][1];
			$thickness 	= GalaxyConfiguration::$galaxy['circleSystemPosition'][$w][2];
			$intensity	= GalaxyConfiguration::$galaxy['circleSystemPosition'][$w][3];

			for ($i = 1; $i <= GalaxyConfiguration::$galaxy['size']; $i++) {
				for ($j = 1; $j <= GalaxyConfiguration::$galaxy['size']; $j++) {
					# current cursor position
					$xPosition = $j;
					$yPosition = $i;

					# calcul de la distance entre la case et le centre
					$d = sqrt(
						pow(abs($xC - $xPosition), 2) + 
						pow(abs($yC - $yPosition), 2)
					);
	
					if ($d >= ($radius - $thickness) && $d <= ($radius + $thickness)) {
						$dtoseg = abs($d - $radius);
						$prob 	= rand(0, 100);

						if (round($intensity - ($dtoseg * $intensity / $thickness)) >= $prob) {
							$type = self::getSystem();

							self::$nbSystem++;
							self::$listSystem[] = array($k, 0, 0, $xPosition, $yPosition, $type);

							$k++;
						}
					}
				}
			}
		}

		# GENERATION PAR VAGUES
		if (GalaxyConfiguration::$galaxy['systemPosition'] !== NULL) {
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

		self::log(self::$nbSystem . ' systèmes générés');
		self::log('_ _ _ _');
	}

	public static function generatePlace() {
		self::log('génération des places');
		$k = 1;

		foreach (self::$listSystem AS $system) {
			$sectorDanger = 0;
			foreach (GalaxyConfiguration::$sectors as $sector) {
				if ($system[1] == $sector['id']) {
					$sectorDanger = $sector['danger'];
					break;
				}
			}

			$place = self::getNbOfPlace($system[5]);

			for ($i = 0; $i < $place; $i++) {
				$type = self::getTypeOfPlace($system[5]);

				if ($type == 1) {
					$pointsRep = rand(1, 10);
					$abilities = [
						'population' => 0,
						'history' => 0,
						'resources' => 0
					];

					# nombre de point a distribuer
					if ($pointsRep < 2) {
						$pointsTot = rand(90, 100);
					} elseif ($pointsRep < 10) {
						$pointsTot = 100;
					} else {
						$pointsTot = rand(100, 120);
					}

					# brassage du tableau
					Utils::shuffle($abilities);

					# répartition
					$z = 1;
					foreach ($abilities as $l => $v) {
						if ($z < 3) {
							$max = $pointsTot - ($z * 10);
							$max = $max < 10 ? 10 : $max;

							$points = rand(10, $max);
							$abilities[$l] = $points;
							$pointsTot -= $points;
						} else {
							$abilities[$l] = $pointsTot < 5 ? 5 : $pointsTot;
						}

						$z++;
					}

					$population = $abilities['population'] * 250 / 100;
					$history 	= $abilities['history'];
					$resources 	= $abilities['resources'];
					$stRES		= 0;
				} elseif ($type == 6) {
					$population = 0;
					$history 	= 0;
					$resources 	= 0;
					$stRES		= 0;
				} else {
					$population = GalaxyConfiguration::$places[$type - 1]['credits'];
					$resources 	= GalaxyConfiguration::$places[$type - 1]['resources'];
					$history 	= GalaxyConfiguration::$places[$type - 1]['history'];
					$stRES		= rand(2000000, 20000000);
				}

				# TODO DANGER
				switch ($sectorDanger) {
					case GalaxyConfiguration::DNG_CASUAL:
						$danger = rand(0,  Place::DNG_CASUAL);
					break;
					case GalaxyConfiguration::DNG_EASY:
						$danger = rand(3,  Place::DNG_EASY);
					break;
					case GalaxyConfiguration::DNG_MEDIUM:
						$danger = rand(6, Place::DNG_MEDIUM);
					break;
					case GalaxyConfiguration::DNG_HARD:
						$danger = rand(9, Place::DNG_HARD);
					break;
					case GalaxyConfiguration::DNG_VERY_HARD:
						$danger = rand(12, Place::DNG_VERY_HARD);
					break;
					default: $danger = 0; break;
				}

				self::$nbPlace++;
				self::$popTotal += $population;
				self::$listPlace[] = array($k, $system[0], $type, ($i + 1), $population, $resources, $history, $stRES, $danger, $danger);
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
				$prime,
				$sector['points']
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

		foreach (self::$listSystem as $v) {
			if ($v[1] == 0) {
				$systemToDelete[] = $v[0];
			}
		}
		
		# suppression des systemes sur des lignes ou des angles
		for ($i = count(self::$listSystem) - 1; $i >= 0; $i--) { 
			if (in_array(self::$listSystem[$i][0], $systemToDelete)) {
				unset(self::$listSystem[$i]);
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

	private static function l2p($x1, $x2, $y1, $y2) {
		return (pow($x1 - $y1, 2) + pow($x2 - $y2, 2));
	}

	private static function distToSegment($p1, $p2, $v1, $v2, $w1, $w2) {
		$l2 = self::l2p($v1, $v2, $w1, $w2);

		if ($l2 == 0) {
			return sqrt(self::l2p($p1, $p2, $v1, $v2));
		}

		$t  = (($p1 - $v1) * ($w1 - $v1) + ($p2 - $v2) * ($w2 - $v2)) / $l2;

		if ($t < 0) {
			return sqrt(self::l2p($p1, $p2, $v1, $v2));
		}

		if ($t > 1) {
			return sqrt(self::l2p($p1, $p2, $w1, $w2));
		}

		$tx = $v1 + $t * ($w1 - $v1);
		$ty = $v2 + $t * ($w2 - $v2);

		return sqrt(self::l2p($p1, $p2, $tx, $ty));
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
}
?>