<?php

namespace Asylamba\Modules\Gaia\Galaxy;

class GalaxyConfigurationV6 extends GalaxyConfiguration {
	public $galaxy = [
		'size' => 250,
		'diag' => 177,
		'mask' => 15,
		'systemProportion'	=> [3, 8, 9, 25, 55],
		'systemPosition'	=> NULL,
		'lineSystemPosition' => [
		#	[[pA], [pB], EPAISSEUR, INTENSITE],
			[[85, 165], [125, 210], 20, 8],
			[[170, 90], [215, 125], 20, 8]
		],
		'circleSystemPosition' => [
		#	[[X1], RAYON, EPAISSEUR, INTENSITE],
			[[-100,-100], 250, 100, 8],
			[[-100,-100], 430, 70, 8]
		],
		'population' => [700, 25000],
	];

	public $sectors = [
		/*[
			'id' => 1,
			'beginColor' => 0,
			'vertices' => [0, 250, 250, 250, 250, 0, 0, 0],
			'barycentre' => [23, 232],
			'display' => [23, 232],
			'name' => 'Secteur 1',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		]*/
		[
			'id' => 1,
			'beginColor' => 0,
			'vertices' => [0, 40, 10, 30, 30, 50, 30, 70, 0, 70],
			'barycentre' => [14, 52],
			'display' => [14, 52],
			'name' => 'Secteur 1',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 2,
			'beginColor' => 0,
			'vertices' => [10, 30, 30, 10, 60, 30, 30, 30, 40, 50, 30, 50],
			'barycentre' => [33, 25],
			'display' => [33, 25],
			'name' => 'Secteur 2',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 3,
			'beginColor' => 0,
			'vertices' => [30, 10, 40, 0, 90, 0, 70, 20, 60, 10, 60, 30],
			'barycentre' => [50, 12],
			'display' => [50, 12],
			'name' => 'Secteur 3',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 4,
			'beginColor' => 0,
			'vertices' => [0, 70, 30, 70, 20, 80, 30, 90, 0, 110],
			'barycentre' => [16, 84],
			'display' => [16, 84],
			'name' => 'Secteur 4',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 5,
			'beginColor' => 7,
			'vertices' => [20, 80, 30, 70, 30, 60, 70, 70, 80, 70, 50, 90, 40, 80],
			'barycentre' => [46, 74],
			'display' => [46, 74],
			'name' => 'Secteur 5',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 6,
			'beginColor' => 2,
			'vertices' => [30, 60, 30, 50, 40, 50, 30, 30, 50, 40, 80, 60, 80, 70, 70, 70],
			'barycentre' => [51, 54],
			'display' => [51, 54],
			'name' => 'Secteur 6',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 7,
			'beginColor' => 1,
			'vertices' => [30, 30, 60, 30, 60, 10, 70, 20, 70, 30, 80, 40, 90, 30, 100, 30, 90, 50, 80, 60, 50, 40],
			'barycentre' => [71, 34],
			'display' => [71, 34],
			'name' => 'Secteur 7',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 8,
			'beginColor' => 4,
			'vertices' => [90, 0, 130, 0, 100, 20, 90, 30, 80, 40, 70, 30, 70, 20],
			'barycentre' => [90, 20],
			'display' => [90, 20],
			'name' => 'Secteur 8',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 9,
			'beginColor' => 7,
			'vertices' => [130, 0, 160, 0, 140, 20, 130, 20, 100, 30, 90, 30, 100, 20],
			'barycentre' => [121, 17],
			'display' => [121, 17],
			'name' => 'Secteur 9',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 10,
			'beginColor' => 3,
			'vertices' => [100, 30, 130, 20, 130, 60, 110, 50, 90, 50],
			'barycentre' => [112, 42],
			'display' => [112, 42],
			'name' => 'Secteur 10',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 11,
			'beginColor' => 4,
			'vertices' => [90, 50, 110, 50, 130, 60, 100, 70, 80, 100, 70, 90, 80, 80, 80, 70, 80, 60],
			'barycentre' => [91, 70],
			'display' => [91, 70],
			'name' => 'Secteur 11',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 12,
			'beginColor' => 3,
			'vertices' => [20, 80, 40, 80, 50, 90, 80, 70, 80, 80, 70, 90, 30, 110, 30, 90],
			'barycentre' => [50, 94],
			'display' => [50, 94],
			'name' => 'Secteur 12',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 13,
			'beginColor' => 1,
			'vertices' => [0, 110, 30, 90, 30, 110, 20, 120, 20, 140, 0, 140],
			'barycentre' => [10, 118],
			'display' => [10, 118],
			'name' => 'Secteur 13',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 14,
			'beginColor' => 0,
			'vertices' => [0, 140, 20, 140, 30, 160, 0, 190],
			'barycentre' => [13, 158],
			'display' => [13, 158],
			'name' => 'Secteur 14',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 15,
			'beginColor' => 0,
			'vertices' => [20, 120, 40, 130, 50, 120, 60, 130, 40, 150, 30, 170, 30, 160, 20, 140],
			'barycentre' => [36, 140],
			'display' => [36, 140],
			'name' => 'Secteur 15',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 16,
			'beginColor' => 2,
			'vertices' => [20, 120, 30, 110, 70, 90, 80, 100, 60, 110, 50, 120, 40, 130],
			'barycentre' => [50, 111],
			'display' => [50, 111],
			'name' => 'Secteur 16',
			'danger' => 1,
			'points' => 1
		], 
		[
			'id' => 17,
			'beginColor' => 0,
			'vertices' => [60, 110, 80, 100, 100, 70, 100, 110, 90, 110],
			'barycentre' => [86, 100],
			'display' => [86, 100],
			'name' => 'Secteur 17',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 18,
			'beginColor' => 0,
			'vertices' => [100, 70, 130, 60, 140, 70, 130, 90, 100, 80],
			'barycentre' => [120, 74],
			'display' => [120, 74],
			'name' => 'Secteur 18',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 19,
			'beginColor' => 0,
			'vertices' => [130, 20, 140, 20, 160, 20, 170, 40, 170, 50, 150, 40, 140, 70, 130, 60],
			'barycentre' => [149, 30],
			'display' => [149, 30],
			'name' => 'Secteur 19',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 20,
			'beginColor' => 0,
			'vertices' => [140, 20, 160, 0, 180, 0, 170, 40, 160, 20],
			'barycentre' => [162, 16],
			'display' => [162, 16],
			'name' => 'Secteur 20',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 21,
			'beginColor' => 0,
			'vertices' => [180, 0, 220, 0, 210, 20, 200, 20, 180, 40, 170, 40],
			'barycentre' => [193, 16],
			'display' => [193, 16],
			'name' => 'Secteur 21',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 22,
			'beginColor' => 0,
			'vertices' => [140, 70, 150, 40, 170, 50, 170, 40, 180, 40, 200, 20, 210, 20, 160, 70, 150, 80],
			'barycentre' => [160, 55],
			'display' => [160, 55],
			'name' => 'Secteur 22',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 23,
			'beginColor' => 0,
			'vertices' => [130, 90, 140, 70, 150, 80, 160, 70, 180, 60, 170, 90, 180, 80, 180, 100, 170, 100, 160, 110, 160, 80, 140, 110, 140, 100],
			'barycentre' => [143, 88],
			'display' => [143, 88],
			'name' => 'Secteur 23',
			'danger' => 3,
			'points' => 1
		], 
		[
			'id' => 24,
			'beginColor' => 0,
			'vertices' => [100, 80, 130, 90, 140, 100, 120, 100, 120, 130, 110, 120, 90, 130, 70, 120, 90, 110, 100, 110],
			'barycentre' => [107, 109],
			'display' => [107, 109],
			'name' => 'Secteur 24',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 25,
			'beginColor' => 0,
			'vertices' => [60, 110, 90, 110, 70, 120, 90, 130, 110, 120, 90, 150, 60, 160, 80, 140, 40, 150, 60, 130, 50, 120],
			'barycentre' => [73, 131],
			'display' => [73, 131],
			'name' => 'Secteur 25',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 26,
			'beginColor' => 0,
			'vertices' => [40, 150, 80, 140, 60, 160, 90, 150, 80, 160, 20, 190, 30, 170],
			'barycentre' => [57, 160],
			'display' => [57, 160],
			'name' => 'Secteur 26',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 27,
			'beginColor' => 0,
			'vertices' => [0, 190, 30, 160, 30, 170, 20, 190, 40, 200, 0, 220],
			'barycentre' => [15, 190],
			'display' => [15, 190],
			'name' => 'Secteur 27',
			'danger' => 2,
			'points' => 1
		], 
		[
			'id' => 28,
			'beginColor' => 0,
			'vertices' => [20, 190, 80, 160, 90, 170, 80, 180, 40, 200],
			'barycentre' => [62, 180],
			'display' => [62, 180],
			'name' => 'Secteur 28',
			'danger' => 3,
			'points' => 1
		], 
		[
			'id' => 29,
			'beginColor' => 0,
			'vertices' => [80, 160, 90, 150, 110, 120, 100, 170, 90, 180, 80, 180, 90, 170],
			'barycentre' => [91, 161],
			'display' => [91, 161],
			'name' => 'Secteur 29',
			'danger' => 3,
			'points' => 1
		], 
		[
			'id' => 30,
			'beginColor' => 0,
			'vertices' => [100, 170, 110, 120, 120, 130, 160, 110],
			'barycentre' => [123, 133],
			'display' => [123, 133],
			'name' => 'Secteur 30',
			'danger' => 3,
			'points' => 1
		], 
		[
			'id' => 31,
			'beginColor' => 0,
			'vertices' => [120, 130, 120, 100, 140, 100, 140, 110, 160, 80, 160, 110],
			'barycentre' => [140, 113],
			'display' => [140, 113],
			'name' => 'Secteur 31',
			'danger' => 3,
			'points' => 1
		], 
		[
			'id' => 32,
			'beginColor' => 0,
			'vertices' => [160, 70, 210, 20, 180, 80, 170, 90, 180, 60],
			'barycentre' => [180, 64],
			'display' => [180, 64],
			'name' => 'Secteur 32',
			'danger' => 3,
			'points' => 1
		], 
		[
			'id' => 33,
			'beginColor' => 0,
			'vertices' => [170, 100, 180, 100, 180, 80, 210, 110, 190, 100, 200, 130],
			'barycentre' => [188, 103],
			'display' => [188, 103],
			'name' => 'Secteur 33',
			'danger' => 3,
			'points' => 2
		], 
		[
			'id' => 34,
			'beginColor' => 0,
			'vertices' => [200, 130, 190, 100, 210, 110, 220, 110, 220, 130, 220, 140, 210, 160, 210, 120, 200, 150, 180, 150],
			'barycentre' => [206, 115],
			'display' => [206, 115],
			'name' => 'Secteur 34',
			'danger' => 3,
			'points' => 2
		], 
		[
			'id' => 35,
			'beginColor' => 0,
			'vertices' => [90, 180, 100, 170, 130, 200, 130, 210, 120, 210, 110, 190, 100, 190],
			'barycentre' => [120, 199],
			'display' => [120, 199],
			'name' => 'Secteur 35',
			'danger' => 3,
			'points' => 2
		], 
		[
			'id' => 36,
			'beginColor' => 0,
			'vertices' => [80, 180, 90, 180, 100, 190, 110, 190, 120, 210, 110, 210],
			'barycentre' => [102, 193],
			'display' => [102, 193],
			'name' => 'Secteur 36',
			'danger' => 3,
			'points' => 2
		], 
		[
			'id' => 37,
			'beginColor' => 0,
			'vertices' => [110, 210, 120, 210, 130, 210, 140, 210, 150, 200, 150, 210, 140, 220, 90, 220],
			'barycentre' => [129, 214],
			'display' => [129, 214],
			'name' => 'Secteur 37',
			'danger' => 4,
			'points' => 2
		], 
		[
			'id' => 38,
			'beginColor' => 0,
			'vertices' => [130, 210, 130, 200, 160, 170, 150, 200, 140, 210],
			'barycentre' => [142, 198],
			'display' => [142, 198],
			'name' => 'Secteur 38',
			'danger' => 3,
			'points' => 2
		], 
		[
			'id' => 39,
			'beginColor' => 0,
			'vertices' => [150, 210, 150, 200, 160, 170, 190, 170, 190, 160, 210, 160, 220, 140, 220, 160, 210, 170, 200, 170, 200, 180, 190, 180, 190, 190, 180, 200, 180, 180],
			'barycentre' => [189, 176],
			'display' => [189, 176],
			'name' => 'Secteur 39',
			'danger' => 4,
			'points' => 3
		], 
		[
			'id' => 40,
			'beginColor' => 0,
			'vertices' => [160, 170, 180, 150, 200, 150, 210, 120, 210, 160, 190, 160, 190, 170],
			'barycentre' => [191, 154],
			'display' => [191, 154],
			'name' => 'Secteur 40',
			'danger' => 3,
			'points' => 2
		], 
		[
			'id' => 41,
			'beginColor' => 0,
			'vertices' => [210, 110, 230, 70, 230, 110, 220, 130, 220, 110],
			'barycentre' => [222, 106],
			'display' => [222, 106],
			'name' => 'Secteur 41',
			'danger' => 4,
			'points' => 2
		], 
		[
			'id' => 42,
			'beginColor' => 0,
			'vertices' => [230, 70, 250, 30, 250, 80, 240, 90],
			'barycentre' => [243, 68],
			'display' => [243, 68],
			'name' => 'Secteur 42',
			'danger' => 5,
			'points' => 5
		], 
		[
			'id' => 43,
			'beginColor' => 0,
			'vertices' => [230, 70, 240, 90, 240, 110, 230, 130, 230, 170, 220, 160, 220, 140, 220, 130, 230, 110],
			'barycentre' => [229, 123],
			'display' => [229, 123],
			'name' => 'Secteur 43',
			'danger' => 4,
			'points' => 3
		], 
		[
			'id' => 44,
			'beginColor' => 0,
			'vertices' => [220, 160, 230, 170, 190, 210, 180, 200, 190, 190, 190, 180, 200, 180, 200, 170, 210, 170],
			'barycentre' => [201, 181],
			'display' => [201, 181],
			'name' => 'Secteur 44',
			'danger' => 4,
			'points' => 2
		], 
		[
			'id' => 45,
			'beginColor' => 0,
			'vertices' => [90, 220, 140, 220, 150, 210, 180, 180, 180, 200, 190, 210, 140, 230, 90, 230],
			'barycentre' => [160, 213],
			'display' => [160, 213],
			'name' => 'Secteur 45',
			'danger' => 4,
			'points' => 3
		], 
		[
			'id' => 46,
			'beginColor' => 0,
			'vertices' => [30, 250, 90, 220, 90, 230, 80, 240, 90, 250],
			'barycentre' => [76, 238],
			'display' => [76, 238],
			'name' => 'Secteur 46',
			'danger' => 5,
			'points' => 5
		], 
		[
			'id' => 47,
			'beginColor' => 0,
			'vertices' => [90, 230, 140, 230, 190, 210, 230, 170, 230, 130, 240, 110, 240, 90, 250, 80, 250, 110, 240, 130, 240, 170, 190, 220, 170, 230, 140, 240, 80, 240],
			'barycentre' => [210, 193],
			'display' => [210, 193],
			'name' => 'Secteur 47',
			'danger' => 4,
			'points' => 1
		], 
		[
			'id' => 48,
			'beginColor' => 0,
			'vertices' => [80, 240, 140, 240, 170, 230, 220, 250, 90, 250],
			'barycentre' => [165, 242],
			'display' => [165, 242],
			'name' => 'Secteur 48',
			'danger' => 5,
			'points' => 3
		], 
		[
			'id' => 49,
			'beginColor' => 0,
			'vertices' => [250, 110, 250, 220, 240, 170, 240, 130],
			'barycentre' => [245, 158],
			'display' => [245, 158],
			'name' => 'Secteur 49',
			'danger' => 5,
			'points' => 3
		], 
		[
			'id' => 50,
			'beginColor' => 0,
			'vertices' => [230, 250, 220, 240, 220, 220, 240, 220, 250, 230, 250, 250],
			'barycentre' => [235, 235],
			'display' => [235, 235],
			'name' => 'Secteur 50',
			'danger' => 5,
			'points' => 10
		]
	];

	public function getSectorCoord($i, $scale = 1, $xTranslate = 0) {
		$sector = $this->sectors[$i - 1]['vertices'];
		foreach ($sector as $k => $v) {
			$sector[$k] = (($v * $scale) + $xTranslate);
		}
		$sector = implode(', ', $sector);
		return $sector;
	}

	public function fillSectorsData() {
		$k = 1;
		echo '<pre>';
		foreach ($this->sectors as $key => $sector) {
			# calculate barycentre
			$strArray = self::getSectorCoord($key + 1);
			$array = explode(', ', $strArray);
			$gx = 0; $gy = 0;
			$vx = 0; $vy = 0;
			$lenght = count($array) / 2;
			for ($j = 0; $j < count($array); $j = $j + 2) {
				$vx += $array[$j];
				$vy += $array[$j + 1];
			}
			$gx = round($vx / $lenght);
			$gy = round($vy / $lenght);
			echo '[' . "\r\n";
			echo '	\'id\' => ' . $k . ',' . "\r\n";
			echo '	\'beginColor\' => ' . $sector['beginColor'] . ',' . "\r\n";
			echo '	\'vertices\' => [' . implode(', ', $sector['vertices']) . '],' . "\r\n";
			echo '	\'barycentre\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
			echo '	\'display\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
			echo '	\'name\' => \'Secteur ' . $k . "',\r\n";
			echo '	\'danger\' => ' . $sector['danger'] . ',' . "\r\n";
			echo '	\'points\' => ' . $sector['points'] . '' . "\r\n";
			echo '], ' . "\r\n";
		
			$k++;
		}
		echo '</pre>';
	}

	public $systems = [
		[
			'id' => 1,
			'name' => 'ruine',
			'placesPropotion' => [0, 0, 85, 10, 0, 5],
			'nbrPlaces' => [2, 6]
		], [
			'id' => 2,
			'name' => 'nébuleuse',
			'placesPropotion' => [0, 0, 5, 90, 0, 5],
			'nbrPlaces' => [2, 8]
		], [
			'id' => 3,
			'name' => 'géante bleue',
			'placesPropotion' => [60, 20, 2, 0, 15, 3],
			'nbrPlaces' => [8, 12]
		], [
			'id' => 4,
			'name' => 'naine jaune',
			'placesPropotion' => [65, 15, 3, 0, 15, 2],
			'nbrPlaces' => [6, 10]
		], [
			'id' => 5,
			'name' => 'naine rouge',
			'placesPropotion' => [75, 10, 3, 0, 10, 2],
			'nbrPlaces' => [3, 6]
		]
	];
	public $places = [
		[
			'id' => 1,
			'name' => 'planète tellurique',
			'resources' => 0,
			'credits' => 0,
			'history' => 0,
		], [
			'id' => 2,
			'name' => 'planète gazeuse',
			'resources' => 38,
			'credits' => 52,
			'history' => 10,
		], [
			'id' => 3,
			'name' => 'ruine',
			'resources' => 5,
			'credits' => 0,
			'history' => 95,
		], [
			'id' => 4,
			'name' => 'poches de gaz',
			'resources' => 0,
			'credits' => 96,
			'history' => 4,
		], [
			'id' => 5,
			'name' => 'ceinture d\'astéroides',
			'resources' => 98,
			'credits' => 0,
			'history' => 2,
		], [
			'id' => 6,
			'name' => 'lieu vide',
			'resources' => 0,
			'credits' => 0,
			'history' => 0,
		]
	];

	# display params
	public $scale = 20;
}
?>