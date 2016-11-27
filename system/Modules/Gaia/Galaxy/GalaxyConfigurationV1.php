<?php
abstract class GalaxyConfigurationV1 {
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
#		'systemPosition'	=> [0, 1, 2, 4, 6, 8, 10, 15, 20, 30, 40, 60, 80, 40, 2, 20, 50, 90, 95, 100],
		'systemPosition'	=> [0, 0, 0, 0, 0, 5, 10, 60, 80, 60, 0, 0, 0, 0, 40, 50, 60, 70, 80, 80],
		'lineSystemPosition' => [
			[[177, 125], [213, 125], 6, 6],
			[[157, 166], [180, 194], 6, 6],
			[[113, 176], [105, 211], 6, 6],
			[[78, 148], [46, 163], 6, 6],
			[[78, 102], [46, 87], 6, 6],
			[[113, 74], [105, 39], 6, 6],
			[[157, 84], [180, 56], 6, 6]
		],
		'circleSystemPosition' => [],
		'population' => [700, 25000],
	];

	public static $sectors = [
		[
			'id' => 1,
			'beginColor' => 1,
			'vertices' =>[120, 105, 138, 108, 145, 125, 138, 142, 120, 145, 107, 133, 107, 117],
			'barycentre' => [125, 125],
			'display' => [125, 125],
			'name' => 'Secteur 1',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 2,
			'beginColor' => 0,
			'vertices' =>[138, 108, 120, 105, 115, 85, 124, 70, 148, 75, 150, 94],
			'barycentre' => [133, 90],
			'display' => [133, 90],
			'name' => 'Secteur 2',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 3,
			'beginColor' => 0,
			'vertices' =>[145, 125, 138, 108, 150, 94, 168, 92, 178, 113, 165, 125],
			'barycentre' => [157, 110],
			'display' => [157, 110],
			'name' => 'Secteur 3',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 1,
		], 
		[
			'id' => 4,
			'beginColor' => 0,
			'vertices' =>[138, 142, 145, 125, 165, 125, 178, 137, 168, 158, 150, 156],
			'barycentre' => [157, 141],
			'display' => [157, 141],
			'name' => 'Secteur 4',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 5,
			'beginColor' => 0,
			'vertices' =>[120, 145, 138, 142, 150, 156, 148, 175, 124, 180, 115, 165],
			'barycentre' => [133, 161],
			'display' => [133, 161],
			'name' => 'Secteur 5',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 6,
			'beginColor' => 1,
			'vertices' =>[107, 133, 120, 145, 115, 165, 102, 175, 82, 158, 88, 141],
			'barycentre' => [102, 153],
			'display' => [102, 153],
			'name' => 'Secteur 6',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 7,
			'beginColor' => 0,
			'vertices' =>[107, 117, 107, 133, 88, 141, 71, 137, 71, 113, 88, 109],
			'barycentre' => [89, 125],
			'display' => [89, 125],
			'name' => 'Secteur 7',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1,
		], 
		[
			'id' => 8,
			'beginColor' => 0,
			'vertices' =>[120, 105, 107, 117, 88, 109, 82, 92, 102, 75, 115, 85],
			'barycentre' => [102, 97],
			'display' => [102, 97],
			'name' => 'Secteur 8',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 9,
			'beginColor' => 0,
			'vertices' =>[124, 70, 115, 85, 102, 75, 94, 48, 103, 33, 118, 42],
			'barycentre' => [109, 59],
			'display' => [109, 59],
			'name' => 'Secteur 9',
			'danger' => GalaxyConfiguration::DNG_VERY_HARD,
			'points' => 2,
		], 
		[
			'id' => 10,
			'beginColor' => 0,
			'vertices' =>[168, 92, 150, 94, 148, 75, 167, 52, 184, 50, 186, 68],
			'barycentre' => [167, 72],
			'display' => [167, 72],
			'name' => 'Secteur 10',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2,
		], 
		[
			'id' => 11,
			'beginColor' => 0,
			'vertices' =>[178, 137, 165, 125, 178, 113, 208, 113, 220, 125, 208, 137],
			'barycentre' => [193, 125],
			'display' => [193, 125],
			'name' => 'Secteur 11',
			'danger' => GalaxyConfiguration::DNG_VERY_HARD,
			'points' => 2,
		], 
		[
			'id' => 12,
			'beginColor' => 0,
			'vertices' =>[148, 175, 150, 156, 168, 158, 186, 182, 184, 200, 167, 198],
			'barycentre' => [167, 178],
			'display' => [167, 178],
			'name' => 'Secteur 12',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2,
		], 
		[
			'id' => 13,
			'beginColor' => 0,
			'vertices' =>[102, 175, 115, 165, 124, 180, 118, 208, 103, 217, 94, 202],
			'barycentre' => [109, 191],
			'display' => [109, 191],
			'name' => 'Secteur 13',
			'danger' => GalaxyConfiguration::DNG_VERY_HARD,
			'points' => 2,
		], 
		[
			'id' => 14,
			'beginColor' => 0,
			'vertices' =>[71, 137, 88, 141, 82, 158, 56, 172, 40, 171, 44, 150],
			'barycentre' => [64, 155],
			'display' => [64, 155],
			'name' => 'Secteur 14',
			'danger' => GalaxyConfiguration::DNG_VERY_HARD,
			'points' => 2,
		], 
		[
			'id' => 15,
			'beginColor' => 0,
			'vertices' =>[82, 92, 88, 109, 71, 113, 44, 100, 40, 79, 56, 78],
			'barycentre' => [64, 95],
			'display' => [64, 95],
			'name' => 'Secteur 15',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2,
		], 
		[
			'id' => 16,
			'beginColor' => 0,
			'vertices' =>[63, 70, 56, 78, 40, 79, 0, 65, 0, 13],
			'barycentre' => [32, 61],
			'display' => [32, 61],
			'name' => 'Secteur 16',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 17,
			'beginColor' => 0,
			'vertices' =>[84, 50, 63, 70, 0, 13, 0, 0, 55, 0],
			'barycentre' => [40, 27],
			'display' => [40, 27],
			'name' => 'Secteur 17',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1,
		], 
		[
			'id' => 18,
			'beginColor' => 0,
			'vertices' =>[103, 33, 94, 48, 84, 50, 55, 0, 96, 0],
			'barycentre' => [86, 26],
			'display' => [86, 26],
			'name' => 'Secteur 18',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1,
		], 
		[
			'id' => 19,
			'beginColor' => 0,
			'vertices' =>[130, 40, 118, 42, 103, 33, 96, 0, 130, 0],
			'barycentre' => [115, 23],
			'display' => [115, 23],
			'name' => 'Secteur 19',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 20,
			'beginColor' => 2,
			'vertices' =>[156, 48, 130, 40, 130, 0, 175, 0],
			'barycentre' => [148, 22],
			'display' => [148, 22],
			'name' => 'Secteur 20',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 21,
			'beginColor' => 2,
			'vertices' =>[184, 50, 167, 52, 156, 48, 175, 0, 223, 0],
			'barycentre' => [181, 30],
			'display' => [181, 30],
			'name' => 'Secteur 21',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 22,
			'beginColor' => 11,
			'vertices' =>[194, 75, 186, 68, 184, 50, 223, 0, 250, 0, 250, 35],
			'barycentre' => [215, 38],
			'display' => [215, 38],
			'name' => 'Secteur 22',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 23,
			'beginColor' => 11,
			'vertices' =>[206, 100, 194, 75, 250, 35, 250, 87],
			'barycentre' => [225, 74],
			'display' => [225, 74],
			'name' => 'Secteur 23',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 24,
			'beginColor' => 0,
			'vertices' =>[220, 125, 208, 113, 206, 100, 250, 87, 250, 125],
			'barycentre' => [227, 110],
			'display' => [227, 110],
			'name' => 'Secteur 24',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 25,
			'beginColor' => 0,
			'vertices' =>[206, 150, 208, 137, 220, 125, 250, 125, 250, 163],
			'barycentre' => [227, 140],
			'display' => [227, 140],
			'name' => 'Secteur 25',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1,
		], 
		[
			'id' => 26,
			'beginColor' => 0,
			'vertices' =>[194, 175, 206, 150, 250, 163, 250, 215],
			'barycentre' => [225, 176],
			'display' => [225, 176],
			'name' => 'Secteur 26',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 1,
		], 
		[
			'id' => 27,
			'beginColor' => 0,
			'vertices' =>[184, 200, 186, 182, 194, 175, 250, 215, 250, 250, 223, 250],
			'barycentre' => [215, 212],
			'display' => [215, 212],
			'name' => 'Secteur 27',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1,
		], 
		[
			'id' => 28,
			'beginColor' => 6,
			'vertices' =>[156, 202, 167, 198, 184, 200, 223, 250, 175, 250],
			'barycentre' => [181, 220],
			'display' => [181, 220],
			'name' => 'Secteur 28',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 29,
			'beginColor' => 6,
			'vertices' =>[130, 210, 156, 202, 175, 250, 130, 250],
			'barycentre' => [148, 228],
			'display' => [148, 228],
			'name' => 'Secteur 29',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 30,
			'beginColor' => 0,
			'vertices' =>[103, 217, 118, 208, 130, 210, 130, 250, 96, 250],
			'barycentre' => [115, 227],
			'display' => [115, 227],
			'name' => 'Secteur 30',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 31,
			'beginColor' => 0,
			'vertices' =>[84, 200, 94, 202, 103, 217, 96, 250, 55, 250],
			'barycentre' => [86, 224],
			'display' => [86, 224],
			'name' => 'Secteur 31',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1,
		], 
		[
			'id' => 32,
			'beginColor' => 0,
			'vertices' =>[63, 180, 84, 200, 55, 250, 0, 250, 0, 237],
			'barycentre' => [40, 223],
			'display' => [40, 223],
			'name' => 'Secteur 32',
			'danger' => GalaxyConfiguration::DNG_HARD,
			'points' => 2,
		], 
		[
			'id' => 33,
			'beginColor' => 0,
			'vertices' =>[40, 171, 56, 172, 63, 180, 0, 237, 0, 185],
			'barycentre' => [32, 189],
			'display' => [32, 189],
			'name' => 'Secteur 33',
			'danger' => GalaxyConfiguration::DNG_MEDIUM,
			'points' => 1,
		], 
		[
			'id' => 34,
			'beginColor' => 0,
			'vertices' =>[38, 140, 44, 150, 40, 171, 0, 185, 0, 145],
			'barycentre' => [24, 158],
			'display' => [24, 158],
			'name' => 'Secteur 34',
			'danger' => GalaxyConfiguration::DNG_EASY,
			'points' => 1,
		], 
		[
			'id' => 35,
			'beginColor' => 7,
			'vertices' =>[38, 110, 38, 140, 0, 145, 0, 105],
			'barycentre' => [19, 125],
			'display' => [19, 125],
			'name' => 'Secteur 35',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
		[
			'id' => 36,
			'beginColor' => 7,
			'vertices' =>[40, 79, 44, 100, 38, 110, 0, 105, 0, 65],
			'barycentre' => [24, 92],
			'display' => [24, 92],
			'name' => 'Secteur 36',
			'danger' => GalaxyConfiguration::DNG_CASUAL,
			'points' => 1,
		], 
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