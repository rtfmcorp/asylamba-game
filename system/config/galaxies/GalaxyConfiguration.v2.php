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
			[[20, 170], [40, 118], 12, 8],
			[[80, 230], [137, 210], 12, 8],
		],
		'circleSystemPosition' => [
		#	[[X1], RAYON, EPAISSEUR, INTENSITE],
			[[-50, 300], 70, 20, 9],
			[[-50, 300], 90, 20, 9],
			[[-50, 300], 110, 20, 9],
			[[-50, 300], 130, 20, 9],

			[[-50, 300], 270, 65, 10],
		],
		'population' => [700, 25000],
	];

	public static $sectors = [
		[
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>[0, 0, 0, 250, 250, 250, 250, 0],
			'barycentre' => [125, 125],
			'display' => [125, 125],
			'name' => 'Secteur 1',
			'danger' => GalaxyConfiguration::DNG_VERY_HARD,
			'points' => 5,
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
			echo '	\'vertices\' =>[' . implode(', ', $sector['vertices']) . '],' . "\r\n";
			echo '	\'barycentre\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
			echo '	\'display\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
			echo '	\'name\' => \'Secteur ' . $k . "',\r\n";
			echo '	\'danger\' => GalaxyConfiguration::DNG_CASUAL' . "\r\n";
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