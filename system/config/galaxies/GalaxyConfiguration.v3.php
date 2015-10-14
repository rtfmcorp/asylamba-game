<?php
abstract class GalaxyConfiguration {
	# general params
	const DNG_CASUAL = 1;
	const DNG_EASY = 2;
	const DNG_MEDIUM = 3;
	const DNG_HARD = 4;
	const DNG_VERY_HARD = 5;

	public static $galaxy = [
		'size' => 250,
		'diag' => 177,
		'mask' => 15,
		'systemProportion'	=> [3, 8, 9, 25, 55],
		'systemPosition'	=> NULL,
		'lineSystemPosition' => [
		#	[[pA], [pB], EPAISSEUR, INTENSITE],
		],
		'circleSystemPosition' => [
		#	[[X1], RAYON, EPAISSEUR, INTENSITE],
			[[-200, 450], 460, 95, 10],
		],
		'population' => [700, 25000],
	];

	public static $sectors = [
		/*[
			'id' => 1,
			'beginColor' => 1,
			'vertices' => [0, 250, 250, 250, 250, 0, 0, 0],
			'barycentre' => [23, 232],
			'display' => [23, 232],
			'name' => 'Secteur 1',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		]*/
		[
			'id' => 1,
			'beginColor' => 8,
			'vertices' => [0, 0, 35, 0, 20, 60, 0, 55],
			'barycentre' => [14, 29],
			'display' => [16, 29],
			'name' => 'Secteur 1',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 2,
			'beginColor' => 8,
			'vertices' => [35, 0, 55, 0, 60, 35, 20, 60],
			'barycentre' => [43, 24],
			'display' => [43, 24],
			'name' => 'Secteur 2',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 3,
			'beginColor' => 0,
			'vertices' => [55, 0, 120, 0, 90, 35, 60, 35],
			'barycentre' => [81, 18],
			'display' => [81, 18],
			'name' => 'Secteur 3',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 4,
			'beginColor' => 0,
			'vertices' => [120, 0, 130, 0, 150, 10, 125, 50, 90, 35],
			'barycentre' => [123, 19],
			'display' => [123, 19],
			'name' => 'Secteur 4',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 5,
			'beginColor' => 0,
			'vertices' => [125, 50, 150, 10, 175, 25, 125, 75],
			'barycentre' => [144, 40],
			'display' => [144, 40],
			'name' => 'Secteur 5',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 1
		], 
		[
			'id' => 6,
			'beginColor' => 0,
			'vertices' => [175, 25, 195, 40, 155, 80, 125, 75],
			'barycentre' => [163, 55],
			'display' => [163, 55],
			'name' => 'Secteur 6',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 7,
			'beginColor' => 0,
			'vertices' => [195, 40, 210, 55, 175, 110, 155, 80],
			'barycentre' => [184, 71],
			'display' => [184, 71],
			'name' => 'Secteur 7',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 1
		], 
		[
			'id' => 8,
			'beginColor' => 0,
			'vertices' => [210, 55, 240, 100, 200, 100, 175, 110],
			'barycentre' => [206, 91],
			'display' => [206, 91],
			'name' => 'Secteur 8',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 9,
			'beginColor' => 0,
			'vertices' => [200, 100, 240, 100, 250, 120, 250, 150, 225, 160],
			'barycentre' => [233, 126],
			'display' => [233, 126],
			'name' => 'Secteur 9',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 1
		], 
		[
			'id' => 10,
			'beginColor' => 0,
			'vertices' => [200, 155, 225, 160, 250, 150, 250, 180, 190, 195],
			'barycentre' => [223, 168],
			'display' => [223, 168],
			'name' => 'Secteur 10',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 11,
			'beginColor' => 0,
			'vertices' => [190, 195, 250, 180, 250, 220, 215, 215],
			'barycentre' => [226, 203],
			'display' => [226, 203],
			'name' => 'Secteur 11',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 1
		], 
		[
			'id' => 12,
			'beginColor' => 0,
			'vertices' => [215, 215, 250, 220, 250, 250, 200, 250],
			'barycentre' => [229, 234],
			'display' => [229, 234],
			'name' => 'Secteur 12',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 13,
			'beginColor' => 0,
			'vertices' => [190, 195, 215, 215, 200, 250, 185, 250, 180, 210],
			'barycentre' => [194, 224],
			'display' => [194, 224],
			'name' => 'Secteur 13',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 14,
			'beginColor' => 12,
			'vertices' => [150, 200, 190, 195, 180, 210, 185, 250, 175, 250],
			'barycentre' => [176, 221],
			'display' => [176, 221],
			'name' => 'Secteur 14',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 2
		], 
		[
			'id' => 15,
			'beginColor' => 12,
			'vertices' => [125, 210, 150, 200, 175, 250, 155, 250],
			'barycentre' => [151, 228],
			'display' => [151, 228],
			'name' => 'Secteur 15',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 16,
			'beginColor' => 0,
			'vertices' => [95, 210, 125, 210, 155, 250, 90, 250, 75, 225],
			'barycentre' => [108, 229],
			'display' => [108, 229],
			'name' => 'Secteur 16',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 17,
			'beginColor' => 0,
			'vertices' => [60, 205, 80, 190, 115, 175, 95, 210, 75, 225],
			'barycentre' => [85, 201],
			'display' => [85, 201],
			'name' => 'Secteur 17',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 18,
			'beginColor' => 0,
			'vertices' => [45, 190, 65, 135, 80, 190, 60, 205],
			'barycentre' => [63, 179],
			'display' => [63, 179],
			'name' => 'Secteur 18',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 1
		], 
		[
			'id' => 19,
			'beginColor' => 0,
			'vertices' => [25, 175, 50, 115, 65, 135, 45, 190],
			'barycentre' => [46, 154],
			'display' => [46, 154],
			'name' => 'Secteur 19',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 20,
			'beginColor' => 0,
			'vertices' => [0, 160, 45, 90, 50, 115, 25, 175],
			'barycentre' => [30, 135],
			'display' => [30, 135],
			'name' => 'Secteur 20',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 21,
			'beginColor' => 0,
			'vertices' => [0, 115, 20, 105, 45, 90, 0, 160],
			'barycentre' => [16, 118],
			'display' => [16, 118],
			'name' => 'Secteur 21',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 22,
			'beginColor' => 8,
			'vertices' => [0, 55, 20, 60, 20, 105, 0, 115],
			'barycentre' => [10, 84],
			'display' => [10, 84],
			'name' => 'Secteur 22',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 23,
			'beginColor' => 0,
			'vertices' => [20, 60, 60, 75, 45, 90, 20, 105],
			'barycentre' => [36, 83],
			'display' => [36, 83],
			'name' => 'Secteur 23',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 24,
			'beginColor' => 0,
			'vertices' => [20, 60, 60, 35, 55, 55, 60, 75],
			'barycentre' => [49, 56],
			'display' => [49, 56],
			'name' => 'Secteur 24',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 25,
			'beginColor' => 0,
			'vertices' => [60, 35, 90, 35, 90, 60, 55, 55],
			'barycentre' => [74, 46],
			'display' => [74, 46],
			'name' => 'Secteur 25',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 26,
			'beginColor' => 0,
			'vertices' => [90, 35, 125, 50, 125, 75, 90, 60],
			'barycentre' => [108, 55],
			'display' => [108, 55],
			'name' => 'Secteur 26',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 27,
			'beginColor' => 0,
			'vertices' => [90, 60, 125, 75, 90, 100, 100, 75],
			'barycentre' => [101, 78],
			'display' => [101, 78],
			'name' => 'Secteur 27',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 28,
			'beginColor' => 4,
			'vertices' => [90, 100, 125, 75, 145, 105, 125, 135],
			'barycentre' => [121, 104],
			'display' => [121, 104],
			'name' => 'Secteur 28',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 29,
			'beginColor' => 4,
			'vertices' => [125, 75, 155, 80, 175, 110, 145, 105],
			'barycentre' => [150, 93],
			'display' => [150, 93],
			'name' => 'Secteur 29',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1
		], 
		[
			'id' => 30,
			'beginColor' => 4,
			'vertices' => [145, 105, 175, 110, 175, 145, 150, 145, 125, 135],
			'barycentre' => [154, 128],
			'display' => [154, 128],
			'name' => 'Secteur 30',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 31,
			'beginColor' => 0,
			'vertices' => [175, 110, 200, 100, 225, 160, 200, 155, 175, 145],
			'barycentre' => [195, 134],
			'display' => [195, 134],
			'name' => 'Secteur 31',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1
		], 
		[
			'id' => 32,
			'beginColor' => 0,
			'vertices' => [150, 145, 175, 145, 200, 155, 190, 195, 180, 170, 155, 160],
			'barycentre' => [175, 162],
			'display' => [175, 162],
			'name' => 'Secteur 32',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 2
		], 
		[
			'id' => 33,
			'beginColor' => 0,
			'vertices' => [180, 170, 190, 195, 150, 200, 150, 185],
			'barycentre' => [168, 188],
			'display' => [168, 188],
			'name' => 'Secteur 33',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 34,
			'beginColor' => 0,
			'vertices' => [130, 185, 150, 185, 150, 200, 125, 210],
			'barycentre' => [139, 195],
			'display' => [139, 195],
			'name' => 'Secteur 34',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 35,
			'beginColor' => 0,
			'vertices' => [115, 175, 130, 185, 125, 210, 95, 210],
			'barycentre' => [116, 195],
			'display' => [116, 195],
			'name' => 'Secteur 35',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 36,
			'beginColor' => 0,
			'vertices' => [65, 135, 90, 150, 90, 170, 115, 175, 80, 190],
			'barycentre' => [88, 164],
			'display' => [88, 164],
			'name' => 'Secteur 36',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 2
		], 
		[
			'id' => 37,
			'beginColor' => 11,
			'vertices' => [65, 135, 95, 125, 110, 135, 90, 150],
			'barycentre' => [90, 136],
			'display' => [90, 136],
			'name' => 'Secteur 37',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 38,
			'beginColor' => 11,
			'vertices' => [50, 115, 75, 105, 95, 125, 65, 135],
			'barycentre' => [71, 120],
			'display' => [71, 120],
			'name' => 'Secteur 38',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 2
		], 
		[
			'id' => 39,
			'beginColor' => 0,
			'vertices' => [45, 90, 60, 75, 75, 105, 50, 115],
			'barycentre' => [58, 96],
			'display' => [58, 96],
			'name' => 'Secteur 39',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 40,
			'beginColor' => 0,
			'vertices' => [55, 55, 90, 60, 100, 75, 60, 75],
			'barycentre' => [76, 66],
			'display' => [76, 66],
			'name' => 'Secteur 40',
			'danger' => GalaxyConfiguration::DNG_VERY_HARD,
			'points' => 5
		], 
		[
			'id' => 41,
			'beginColor' => 0,
			'vertices' => [60, 75, 100, 75, 90, 100, 95, 125, 75, 105],
			'barycentre' => [84, 96],
			'display' => [84, 96],
			'name' => 'Secteur 41',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 42,
			'beginColor' => 0,
			'vertices' => [90, 100, 125, 135, 110, 135, 95, 125],
			'barycentre' => [105, 124],
			'display' => [105, 124],
			'name' => 'Secteur 42',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 43,
			'beginColor' => 0,
			'vertices' => [110, 135, 125, 135, 130, 160, 115, 150],
			'barycentre' => [120, 145],
			'display' => [120, 145],
			'name' => 'Secteur 43',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 44,
			'beginColor' => 10,
			'vertices' => [125, 135, 150, 145, 155, 160, 130, 160],
			'barycentre' => [140, 150],
			'display' => [140, 150],
			'name' => 'Secteur 44',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1
		], 
		[
			'id' => 45,
			'beginColor' => 10,
			'vertices' => [130, 160, 155, 160, 180, 170, 150, 185],
			'barycentre' => [154, 169],
			'display' => [154, 169],
			'name' => 'Secteur 45',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 2
		], 
		[
			'id' => 46,
			'beginColor' => 0,
			'vertices' => [130, 160, 150, 185, 130, 185, 115, 175],
			'barycentre' => [131, 176],
			'display' => [131, 176],
			'name' => 'Secteur 46',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		], 
		[
			'id' => 47,
			'beginColor' => 0,
			'vertices' => [115, 150, 130, 160, 115, 175, 90, 170],
			'barycentre' => [113, 164],
			'display' => [113, 164],
			'name' => 'Secteur 47',
			'danger' => GalaxyConfiguration::DNG_VERY_HARD,
			'points' => 5
		], 
		[
			'id' => 48,
			'beginColor' => 0,
			'vertices' => [90, 150, 110, 135, 115, 150, 90, 170],
			'barycentre' => [101, 151],
			'display' => [101, 151],
			'name' => 'Secteur 48',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2
		]
	];

	public static function getSectorCoord($i, $scale = 1, $xTranslate = 0) {
		$sector = self::$sectors[$i - 1]['vertices'];

		foreach ($sector as $k => $v) {
			$sector[$k] = (($v * $scale) + $xTranslate);
		}

		$sector = implode(', ', $sector);
		return $sector;
	}

	public static function fillSectorsData() {
		$k = 1;

		echo '<pre>';
		foreach (self::$sectors as $key => $sector) {
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
			echo '	\'danger\' => GalaxyConfiguration::DNG_CASUAL,' . "\r\n";
			echo '	\'points\' => 1' . "\r\n";
			echo '], ' . "\r\n";
		
			$k++;
		}
		echo '</pre>';
	}

	public static $systems = [
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

	public static $places = [
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
	public static $scale = 20;
}
?>