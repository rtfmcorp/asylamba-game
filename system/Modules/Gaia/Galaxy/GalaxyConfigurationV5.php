<?php

namespace Asylamba\Modules\Gaia\Galaxy;

class GalaxyConfigurationV5 extends GalaxyConfiguration {
	public $galaxy = [
		'size' => 250,
		'diag' => 177,
		'mask' => 15,
		'systemProportion'	=> [3, 8, 9, 25, 55],
		'systemPosition'	=> NULL,
		'lineSystemPosition' => [
		#	[[pA], [pB], EPAISSEUR, INTENSITE],
			[[10, 130], [60, 130], 10, 8],
			[[30, 30], [80, 80], 7, 8],
			[[120, 10], [120, 60], 10, 8],
			[[230, 30], [170, 80], 7, 8],
			[[240, 120], [190, 120], 10, 8],
			[[220, 220], [170, 170], 7, 8],
			[[130, 240], [130, 190], 10, 8],
			[[30, 220], [80, 170], 7, 8]
		],
		'circleSystemPosition' => [
		#	[[X1], RAYON, EPAISSEUR, INTENSITE],
			[[125,125], 130, 50, 9],
			[[125,125], 40, 40, 9]
		],
		'population' => [700, 25000],
	];

	public $sectors = [
		[
			'id' => 1,
			'beginColor' => 0,
			'vertices' => [90, 110, 100, 100, 110, 90, 120, 90, 140, 90, 130, 110, 120, 120, 110, 120],
			'barycentre' => [115, 99],
			'display' => [115, 99],
			'name' => 'Secteur 1',
			'danger' => self::DNG_VERY_HARD,
			'points' => 3
		], 
		[
			'id' => 2,
			'beginColor' => 0,
			'vertices' => [120, 120, 130, 110, 140, 90, 150, 100, 160, 110, 160, 120, 160, 140],
			'barycentre' => [146, 113],
			'display' => [146, 113],
			'name' => 'Secteur 2',
			'danger' => self::DNG_VERY_HARD,
			'points' => 5
		], 
		[
			'id' => 3,
			'beginColor' => 0,
			'vertices' => [120, 120, 160, 140, 150, 150, 140, 160, 130, 160, 110, 160, 120, 140],
			'barycentre' => [133, 143],
			'display' => [133, 143],
			'name' => 'Secteur 3',
			'danger' => self::DNG_VERY_HARD,
			'points' => 3
		], 
		[
			'id' => 4,
			'beginColor' => 0,
			'vertices' => [120, 120, 120, 140, 110, 160, 100, 150, 90, 140, 90, 130, 90, 110, 110, 120],
			'barycentre' => [104, 134],
			'display' => [104, 134],
			'name' => 'Secteur 4',
			'danger' => self::DNG_VERY_HARD,
			'points' => 5
		], 
		[
			'id' => 5,
			'beginColor' => 0,
			'vertices' => [70, 130, 90, 130, 90, 140, 100, 150, 90, 170, 80, 160, 70, 150],
			'barycentre' => [84, 147],
			'display' => [84, 147],
			'name' => 'Secteur 5',
			'danger' => self::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 6,
			'beginColor' => 0,
			'vertices' => [70, 100, 80, 90, 100, 100, 90, 110, 90, 130, 70, 130],
			'barycentre' => [83, 110],
			'display' => [83, 110],
			'name' => 'Secteur 6',
			'danger' => self::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 7,
			'beginColor' => 0,
			'vertices' => [100, 70, 120, 70, 120, 90, 110, 90, 100, 100, 80, 90],
			'barycentre' => [105, 80],
			'display' => [105, 80],
			'name' => 'Secteur 7',
			'danger' => self::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 8,
			'beginColor' => 0,
			'vertices' => [120, 70, 150, 70, 160, 80, 150, 100, 140, 90, 120, 90],
			'barycentre' => [140, 80],
			'display' => [140, 80],
			'name' => 'Secteur 8',
			'danger' => self::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 9,
			'beginColor' => 0,
			'vertices' => [150, 100, 160, 80, 180, 100, 180, 120, 160, 120, 160, 110],
			'barycentre' => [165, 105],
			'display' => [165, 105],
			'name' => 'Secteur 9',
			'danger' => self::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 10,
			'beginColor' => 0,
			'vertices' => [160, 120, 180, 120, 180, 150, 170, 160, 150, 150, 160, 140],
			'barycentre' => [167, 140],
			'display' => [167, 140],
			'name' => 'Secteur 10',
			'danger' => self::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 11,
			'beginColor' => 0,
			'vertices' => [150, 150, 170, 160, 150, 180, 130, 180, 130, 160, 140, 160],
			'barycentre' => [145, 165],
			'display' => [145, 165],
			'name' => 'Secteur 11',
			'danger' => self::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 12,
			'beginColor' => 0,
			'vertices' => [130, 160, 130, 180, 100, 180, 90, 170, 100, 150, 110, 160],
			'barycentre' => [110, 167],
			'display' => [110, 167],
			'name' => 'Secteur 12',
			'danger' => self::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 13,
			'beginColor' => 0,
			'vertices' => [80, 160, 90, 170, 100, 180, 130, 190, 120, 200, 90, 200, 75, 185, 80, 180, 80, 170],
			'barycentre' => [94, 186],
			'display' => [94, 186],
			'name' => 'Secteur 13',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 14,
			'beginColor' => 0,
			'vertices' => [50, 140, 60, 130, 70, 100, 70, 130, 70, 150, 80, 160, 80, 170, 70, 170, 65, 175, 50, 160],
			'barycentre' => [60, 149],
			'display' => [60, 149],
			'name' => 'Secteur 14',
			'danger' => self::DNG_EASY,
			'points' => 2
		], 
		[
			'id' => 15,
			'beginColor' => 0,
			'vertices' => [50, 120, 50, 90, 65, 75, 70, 80, 80, 80, 80, 70, 100, 70, 80, 90, 70, 100, 60, 130],
			'barycentre' => [60, 91],
			'display' => [60, 91],
			'name' => 'Secteur 15',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 16,
			'beginColor' => 0,
			'vertices' => [80, 70, 75, 65, 90, 50, 110, 50, 120, 60, 130, 50, 150, 70, 120, 70, 100, 70],
			'barycentre' => [98, 60],
			'display' => [98, 60],
			'name' => 'Secteur 16',
			'danger' => self::DNG_EASY,
			'points' => 2
		], 
		[
			'id' => 17,
			'beginColor' => 0,	
			'vertices' => [130, 50, 160, 50, 175, 65, 170, 70, 170, 80, 180, 80, 185, 75, 200, 90, 180, 100, 160, 80, 150, 70],
			'barycentre' => [159, 64],
			'display' => [159, 64],
			'name' => 'Secteur 17',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 18,
			'beginColor' => 0,
			'vertices' => [180, 100, 200, 90, 200, 110, 190, 120, 200, 130, 180, 150, 180, 120],
			'barycentre' => [185, 112],
			'display' => [185, 112],
			'name' => 'Secteur 18',
			'danger' => self::DNG_EASY,
			'points' => 2
		], 
		[
			'id' => 19,
			'beginColor' => 0,
			'vertices' => [200, 130, 200, 160, 185, 175, 180, 170, 170, 170, 170, 180, 150, 180, 170, 160, 180, 150],
			'barycentre' => [178, 162],
			'display' => [178, 162],
			'name' => 'Secteur 19',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 20,
			'beginColor' => 0,
			'vertices' => [100, 180, 130, 180, 150, 180, 170, 180, 175, 185, 160, 200, 140, 200, 130, 190],
			'barycentre' => [144, 187],
			'display' => [144, 187],
			'name' => 'Secteur 20',
			'danger' => self::DNG_EASY,
			'points' => 2
		], 
		[
			'id' => 21,
			'beginColor' => 0,
			'vertices' => [120, 200, 130, 190, 140, 200, 140, 230, 130, 240, 120, 230],
			'barycentre' => [130, 215],
			'display' => [130, 215],
			'name' => 'Secteur 21',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 22,
			'beginColor' => 0,
			'vertices' => [70, 170, 80, 170, 80, 180, 75, 185, 55, 205, 40, 220, 30, 220, 30, 210, 45, 195, 65, 175],
			'barycentre' => [55, 193],
			'display' => [55, 193],
			'name' => 'Secteur 22',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 23,
			'beginColor' => 0,
			'vertices' => [20, 120, 50, 120, 60, 130, 50, 140, 20, 140, 10, 130],
			'barycentre' => [35, 130],
			'display' => [35, 130],
			'name' => 'Secteur 23',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 24,
			'beginColor' => 0,
			'vertices' => [30, 30, 40, 30, 55, 45, 75, 65, 80, 70, 80, 80, 70, 80, 65, 75, 45, 55, 30, 40],
			'barycentre' => [57, 57],
			'display' => [57, 57],
			'name' => 'Secteur 24',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 25,
			'beginColor' => 0,
			'vertices' => [120, 10, 130, 20, 130, 50, 120, 60, 110, 50, 110, 20],
			'barycentre' => [120, 35],
			'display' => [120, 35],
			'name' => 'Secteur 25',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 26,
			'beginColor' => 0,
			'vertices' => [220, 30, 220, 40, 205, 55, 185, 75, 180, 80, 170, 80, 170, 70, 175, 65, 195, 45, 210, 30],
			'barycentre' => [190, 57],
			'display' => [190, 57],
			'name' => 'Secteur 26',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 27,
			'beginColor' => 0,
			'vertices' => [190, 120, 200, 110, 230, 110, 240, 120, 230, 130, 200, 130],
			'barycentre' => [215, 120],
			'display' => [215, 120],
			'name' => 'Secteur 27',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 28,
			'beginColor' => 0,
			'vertices' => [170, 170, 180, 170, 185, 175, 205, 195, 220, 210, 220, 220, 210, 220, 195, 205, 175, 185, 170, 180],
			'barycentre' => [193, 193],
			'display' => [193, 193],
			'name' => 'Secteur 28',
			'danger' => self::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 29,
			'beginColor' => 0,
			'vertices' => [140, 230, 170, 230, 195, 205, 210, 220, 220, 220, 250, 250, 170, 250],
			'barycentre' => [194, 229],
			'display' => [194, 229],
			'name' => 'Secteur 29',
			'danger' => self::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 30,
			'beginColor' => 2,
			'vertices' => [90, 250, 120, 230, 130, 240, 140, 230, 170, 250],
			'barycentre' => [132, 243],
			'display' => [132, 243],
			'name' => 'Secteur 30',
			'danger' => self::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 31,
			'beginColor' => 2,
			'vertices' => [0, 250, 30, 220, 40, 220, 55, 205, 90, 230, 120, 230, 90, 250],
			'barycentre' => [61, 229],
			'display' => [61, 229],
			'name' => 'Secteur 31',
			'danger' => self::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 32,
			'beginColor' => 0,
			'vertices' => [0, 170, 20, 140, 20, 170, 45, 195, 30, 210, 30, 220, 0, 250],
			'barycentre' => [21, 194],
			'display' => [21, 194],
			'name' => 'Secteur 32',
			'danger' => self::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 33,
			'beginColor' => 7,
			'vertices' => [0, 80, 20, 120, 10, 130, 20, 140, 0, 170],
			'barycentre' => [7, 122],
			'display' => [7, 122],
			'name' => 'Secteur 33',
			'danger' => self::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 34,
			'beginColor' => 7,
			'vertices' => [0, 0, 30, 30, 30, 40, 45, 55, 20, 80, 20, 120, 0, 80],
			'barycentre' => [21, 58],
			'display' => [21, 58],
			'name' => 'Secteur 34',
			'danger' => self::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 35,
			'beginColor' => 0,
			'vertices' => [0, 0, 80, 0, 110, 20, 80, 20, 55, 45, 40, 30, 30, 30],
			'barycentre' => [56, 21],
			'display' => [56, 21],
			'name' => 'Secteur 35',
			'danger' => self::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 36,
			'beginColor' => 3,
			'vertices' => [80, 0, 170, 0, 130, 20, 120, 10, 110, 20],
			'barycentre' => [123, 4],
			'display' => [123, 4],
			'name' => 'Secteur 36',
			'danger' => self::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 37,
			'beginColor' => 3,
			'vertices' => [170, 0, 250, 0, 220, 30, 210, 30, 195, 45, 170, 20, 130, 20],
			'barycentre' => [192, 21],
			'display' => [192, 21],
			'name' => 'Secteur 37',
			'danger' => self::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 38,
			'beginColor' => 0,
			'vertices' => [205, 55, 220, 40, 220, 40, 220, 30, 250, 0, 250, 80, 230, 110, 230, 80],
			'barycentre' => [228, 54],
			'display' => [228, 54],
			'name' => 'Secteur 38',
			'danger' => self::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 39,
			'beginColor' => 6,
			'vertices' => [250, 80, 250, 160, 230, 130, 240, 120, 230, 110],
			'barycentre' => [243, 122],
			'display' => [243, 122],
			'name' => 'Secteur 39',
			'danger' => self::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 40,
			'beginColor' => 6,
			'vertices' => [230, 130, 250, 160, 250, 250, 220, 220, 220, 210, 205, 195, 230, 170],
			'barycentre' => [229, 191],
			'display' => [229, 191],
			'name' => 'Secteur 40',
			'danger' => self::DNG_CASUAL,
			'points' => 1
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
			echo '	\'beginColor\' => 0,' . "\r\n";
			echo '	\'vertices\' => [' . implode(', ', $sector['vertices']) . '],' . "\r\n";
			echo '	\'barycentre\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
			echo '	\'display\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
			echo '	\'name\' => \'Secteur ' . $k . "',\r\n";
			echo '	\'danger\' => self::DNG_CASUAL,' . "\r\n";
			echo '	\'points\' => 1' . "\r\n";
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
