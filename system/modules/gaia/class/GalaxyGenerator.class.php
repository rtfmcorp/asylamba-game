<?php
abstract class GalaxyGenerator {
	# stats
	public $nbSystem = 0;
	public $listSystem = array();

	public function generate() {
		# faire des trucs
		$this->generateSystem();
		# $gc->generatePlace();
		# $gc->generateSector();
		# $gc->associateSystemToSector();
		# $gc->save();
	}

	private function generateSystem() {
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
				
				if ($this->isPointInMap($d2o)) {
					$type = $this->getSystem();

					$this->nbSystem++;
					$this->listSystem[] = array($k, 0, 0, $xPosition, $yPosition, $type);

					$k++;
				}
			}
		}
	}

	public function generatePlace() {
		$k = 1;

		foreach ($this->listSystem AS $system) {
			$place = $this->getNbOfPlace($system[5]);

			for ($i = 0; $i < $place; $i++) {
				$type = $this->randomPlace($system[5]);

				$population = $this->randomPopulation($type);
				$history    = $this->randomHistory($type);
				$resources  = $this->randomResources($type);

				$this->nbrOfPlace++;
				$this->listPlace[] = array($k, 0, $system[0], $type, ($i + 1), $population, $history, $resources);
				$k++;
			}
		}
	}

	private function isPointInMap($d2o) {
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
				} elseif ($d< 36) {
					if ($random < 50)  { return TRUE; }
				} elseif($d2o < 38) {
					if ($random < 50)   { return TRUE; }
				} else {
					if ($random < 10)   { return TRUE; }
				}
			}
			return FALSE;
		} else {
			return FALSE;
		}
	}

	private function getProportion($params, $value) {
		$cursor = 0;
		$min = 0;
		$max = 0;

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

			$cursor += $params[$i];

			if ($value <= $min && $value < $max) {
				return array($i);
			}
		}
	}

	private function getSystem() {
		return $this->getProportion(GalaxyConfiguration::$galaxy['systemProportion'], rand(0, 100)) + 1;
	}

	private function getNbOfPlace($systemType) {
		return rand(
			GalaxyConfiguration::$systems[$systemType - 1]['nbrPlaces'][0],
			GalaxyConfiguration::$systems[$systemType - 1]['nbrPlaces'][1]
		);
	}
}
?>