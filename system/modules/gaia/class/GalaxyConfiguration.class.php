<?php
abstract class GalaxyConfiguration {
	# general params
	public static $galaxy = [
		'size' => 250,
		'diag' => 177,
		'mask' => 12,
		'systemProportion'	=> [3, 8, 9, 25, 55],
#		'systemPosition'	=> [0, 1, 2, 4, 6, 8, 10, 15, 20, 30, 40, 60, 80, 40, 2, 20, 50, 90, 95, 100],
		'systemPosition'	=> [0, 0, 0, 0, 0, 5, 10, 60, 80, 60, 0, 0, 0, 0, 60, 80, 60, 10, 0, 0],
		'lineSystemPosition' => [
			[[125,  36], [125,  64], 8],
			[[125, 186], [125, 214], 8],
			[[35 , 125], [64 , 125], 8],
			[[186, 125], [214, 125], 8]
		],
		'population' => [700, 25000],
	];

	public static $sectors = [
		[
			'id' => 1,
			'beginColor' => 1,
			'vertices' =>[105, 135, 115, 145, 135, 145, 145, 135, 145, 115, 135, 105, 115, 105, 105, 115],
			'barycentre' => [125, 125],
			'display' => [125, 125],
			'name' => '1'
		], [
			'id' => 2,
			'beginColor' => 1,
			'vertices' =>[145, 115, 135, 105, 122, 105, 123, 84, 138, 83, 136, 90, 158, 92, 163, 83, 177, 100, 182, 107, 165, 129, 145, 127],
			'barycentre' => [149, 102],
			'display' => [149, 102],
			'name' => '2'
		], [
			'id' => 3,
			'beginColor' => 0,
			'vertices' =>[145, 127, 165, 129, 181, 154, 185, 160, 156, 173, 122, 166, 123, 145, 135, 145, 145, 135],
			'barycentre' => [151, 148],
			'display' => [151, 148],
			'name' => '3'
		], [
			'id' => 4,
			'beginColor' => 0,
			'vertices' =>[105, 127, 105, 135, 115, 145, 123, 145, 122, 166, 100, 170, 78, 165, 68, 145, 89, 128],
			'barycentre' => [101, 147],
			'display' => [101, 147],
			'name' => '4'
		], [
			'id' => 5,
			'beginColor' => 0,
			'vertices' =>[105, 127, 89, 128, 65, 120, 60, 111, 65, 95, 92, 95, 101, 86, 111, 85, 123, 84, 122, 105, 115, 105, 105, 115],
			'barycentre' => [96, 105],
			'display' => [96, 105],
			'name' => '5'
		], [
			'id' => 6,
			'beginColor' => 0,
			'vertices' =>[62, 68, 86, 82, 106, 69, 111, 85, 101, 86, 92, 95, 65, 95, 60, 111, 46, 85],
			'barycentre' => [81, 86],
			'display' => [81, 86],
			'name' => '6'
		], [
			'id' => 7,
			'beginColor' => 2,
			'vertices' =>[62, 68, 60, 48, 71, 47, 69, 38, 90, 34, 107, 38, 100, 50, 106, 69, 86, 82],
			'barycentre' => [83, 53],
			'display' => [83, 53],
			'name' => '7'
		], [
			'id' => 8,
			'beginColor' => 0,
			'vertices' =>[122, 36, 148, 48, 138, 83, 123, 84, 111, 85, 100, 50, 107, 38, 115, 39],
			'barycentre' => [121, 58],
			'display' => [121, 58],
			'name' => '8'
		], [
			'id' => 9,
			'beginColor' => 0,
			'vertices' =>[158, 92, 136, 90, 148, 48, 185, 45, 198, 46, 196, 59, 178, 55, 163, 83],
			'barycentre' => [170, 65],
			'display' => [170, 65],
			'name' => '9'
		], [
			'id' => 10,
			'beginColor' => 0,
			'vertices' =>[217, 100, 177, 100, 163, 83, 178, 55, 187, 86, 208, 82],
			'barycentre' => [188, 84],
			'display' => [188, 84],
			'name' => '10'
		], [
			'id' => 11,
			'beginColor' => 0,
			'vertices' =>[182, 107, 194, 123, 209, 122, 209, 146, 181, 154, 165, 129],
			'barycentre' => [190, 130],
			'display' => [190, 130],
			'name' => '11'
		], [
			'id' => 12,
			'beginColor' => 0,
			'vertices' =>[122, 166, 156, 173, 185, 160, 191, 168, 195, 175, 190, 199, 172, 188, 138, 186],
			'barycentre' => [169, 177],
			'display' => [169, 177],
			'name' => '12'
		], [
			'id' => 13,
			'beginColor' => 0,
			'vertices' =>[172, 188, 190, 199, 199, 204, 176, 215, 143, 207, 138, 186],
			'barycentre' => [170, 200],
			'display' => [170, 200],
			'name' => '13'
		], [
			'id' => 14,
			'beginColor' => 5,
			'vertices' =>[103, 185, 138, 186, 143, 207, 125, 230, 101, 202],
			'barycentre' => [122, 202],
			'display' => [122, 202],
			'name' => '14'
		], [
			'id' => 15,
			'beginColor' => 0,
			'vertices' =>[78, 165, 100, 170, 122, 166, 138, 186, 103, 185, 101, 202, 88, 204],
			'barycentre' => [104, 183],
			'display' => [104, 183],
			'name' => '15'
		], [
			'id' => 16,
			'beginColor' => 6,
			'vertices' =>[70, 167, 59, 195, 35, 187, 27, 206, 48, 203, 72, 207, 88, 204, 78, 165],
			'barycentre' => [60, 192],
			'display' => [60, 192],
			'name' => '16'
		], [
			'id' => 17,
			'beginColor' => 0,
			'vertices' =>[35, 138, 65, 120, 89, 128, 68, 145, 78, 165, 70, 167],
			'barycentre' => [68, 144],
			'display' => [68, 144],
			'name' => '17'
		], [
			'id' => 18,
			'beginColor' => 7,
			'vertices' =>[0, 132, 0, 110, 25, 105, 60, 111, 65, 120, 35, 138],
			'barycentre' => [31, 119],
			'display' => [31, 119],
			'name' => '18'
		], [
			'id' => 19,
			'beginColor' => 7,
			'vertices' =>[0, 110, 0, 87, 22, 82, 46, 85, 60, 111, 25, 105],
			'barycentre' => [26, 97],
			'display' => [26, 97],
			'name' => '19'
		], [
			'id' => 20,
			'beginColor' => 0,
			'vertices' =>[0, 87, 0, 41, 35, 48, 60, 48, 62, 68, 46, 85, 22, 82],
			'barycentre' => [32, 66],
			'display' => [32, 66],
			'name' => '20'
		], [
			'id' => 21,
			'beginColor' => 0,
			'vertices' =>[0, 0, 0, 41, 35, 48, 60, 48, 71, 47, 69, 38, 67, 30, 73, 10, 65, 0],
			'barycentre' => [49, 29],
			'display' => [49, 29],
			'name' => '21'
		], [
			'id' => 22,
			'beginColor' => 2,
			'vertices' =>[65, 0, 113, 0, 135, 28, 122, 36, 115, 39, 107, 38, 90, 34, 69, 38, 67, 30, 73, 10],
			'barycentre' => [96, 25],
			'display' => [96, 25],
			'name' => '22'
		], [
			'id' => 23,
			'beginColor' => 0,
			'vertices' =>[113, 0, 185, 0, 180, 19, 185, 45, 148, 48, 122, 36, 135, 28],
			'barycentre' => [153, 25],
			'display' => [153, 25],
			'name' => '23'
		], [
			'id' => 24,
			'beginColor' => 3,
			'vertices' =>[185, 0, 250, 0, 250, 40, 230, 49, 198, 46, 185, 45, 180, 19],
			'barycentre' => [211, 28],
			'display' => [211, 28],
			'name' => '24'
		], [
			'id' => 25,
			'beginColor' => 3,
			'vertices' =>[250, 40, 250, 85, 235, 77, 208, 82, 187, 86, 178, 55, 196, 59, 198, 46, 230, 49],
			'barycentre' => [215, 64],
			'display' => [215, 64],
			'name' => '25'
		], [
			'id' => 26,
			'beginColor' => 0,
			'vertices' =>[250, 85, 250, 100, 217, 100, 208, 82, 235, 77],
			'barycentre' => [232, 89],
			'display' => [232, 89],
			'name' => '26'
		], [
			'id' => 27,
			'beginColor' => 0,
			'vertices' =>[250, 100, 250, 120, 209, 122, 194, 123, 182, 107, 177, 100, 217, 100],
			'barycentre' => [211, 110],
			'display' => [211, 110],
			'name' => '27'
		], [
			'id' => 28,
			'beginColor' => 4,
			'vertices' =>[250, 120, 250, 162, 191, 168, 185, 160, 181, 154, 209, 146, 209, 122],
			'barycentre' => [211, 147],
			'display' => [211, 147],
			'name' => '28'
		], [
			'id' => 29,
			'beginColor' => 4,
			'vertices' =>[250, 162, 250, 202, 199, 204, 190, 199, 195, 175, 191, 168],
			'barycentre' => [213, 185],
			'display' => [213, 185],
			'name' => '29'
		], [
			'id' => 30,
			'beginColor' => 0,
			'vertices' =>[250, 202, 250, 250, 181, 250, 176, 215, 199, 204],
			'barycentre' => [211, 224],
			'display' => [211, 224],
			'name' => '30'
		], [
			'id' => 31,
			'beginColor' => 5,
			'vertices' =>[181, 250, 123, 250, 125, 230, 143, 207, 176, 215],
			'barycentre' => [150, 230],
			'display' => [150, 230],
			'name' => '31'
		], [
			'id' => 32,
			'beginColor' => 0,
			'vertices' =>[123, 250, 73, 250, 72, 207, 88, 204, 101, 202, 125, 230],
			'barycentre' => [97, 224],
			'display' => [97, 224],
			'name' => '32'
		], [
			'id' => 33,
			'beginColor' => 0,
			'vertices' =>[73, 250, 0, 250, 0, 210, 27, 206, 48, 203, 72, 207],
			'barycentre' => [37, 221],
			'display' => [37, 221],
			'name' => '33'
		], [
			'id' => 34,
			'beginColor' => 6,
			'vertices' =>[0, 180, 70, 167, 59, 195, 35, 187, 27, 206, 0, 210],
			'barycentre' => [32, 191],
			'display' => [32, 191],
			'name' => '34'
		], [
			'id' => 35,
			'beginColor' => 0,
			'vertices' =>[0, 132, 35, 138, 70, 167, 0, 180],
			'barycentre' => [26, 154],
			'display' => [26, 154],
			'name' => '35'
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
			echo '	\'vertices\' =>[' . implode(', ', $sector['vertices']) . '],' . "\r\n";
			echo '	\'barycentre\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
			echo '	\'display\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
			echo '], ' . "\r\n";
		
			$k++;
		}
		echo '</pre>';
	}

	public static $systems = [
		[
			'id' => 1,
			'name' => 'ruine',
			'placesPropotion' => [0, 0, 80, 10, 0, 10],
			'nbrPlaces' => [1, 5]
		], [
			'id' => 2,
			'name' => 'nébuleuse',
			'placesPropotion' => [5, 5, 10, 75, 0, 5],
			'nbrPlaces' => [2, 8]
		], [
			'id' => 3,
			'name' => 'géante bleue',
			'placesPropotion' => [40, 25, 10, 0, 20, 5],
			'nbrPlaces' => [12, 18]
		], [
			'id' => 4,
			'name' => 'naine jaune',
			'placesPropotion' => [55, 20, 5, 0, 15, 5],
			'nbrPlaces' => [6, 12]
		], [
			'id' => 5,
			'name' => 'naine rouge',
			'placesPropotion' => [70, 10, 5, 0, 10, 5],
			'nbrPlaces' => [2, 6]
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
			'credits' => 44,
			'history' => 18,
		], [
			'id' => 3,
			'name' => 'ruine',
			'resources' => 5,
			'credits' => 0,
			'history' => 95,
		], [
			'id' => 4,
			'name' => 'poches de gaz',
			'resources' => 4,
			'credits' => 92,
			'history' => 4,
		], [
			'id' => 5,
			'name' => 'ceinture d\'astéroides',
			'resources' => 90,
			'credits' => 6,
			'history' => 4,
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