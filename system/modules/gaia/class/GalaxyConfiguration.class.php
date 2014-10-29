<?php
abstract class GalaxyConfiguration {
	# general params
	public static $galaxy = array(
		'size' => 250,
		'diag' => 177,
		'mask' => 12,
		'systemProportion' => array(3, 8, 9, 25, 55),
		'systemPosition' => array(0, 1, 2, 4, 6, 8, 10, 15, 20, 30, 40, 60, 80, 40, 2, 20, 50, 90, 95, 100),
		'population' => array(700, 25000),
	);

	public static $sectors = array(
		array(
			'id' => 1,
			'beginColor' => 1,
			'vertices' =>array(105, 135, 115, 145, 135, 145, 145, 135, 145, 115, 135, 105, 115, 105, 105, 115),
			'barycentre' => array(125, 125),
			'display' => array(125, 125),
			'name' => '1'
		),
		array(
			'id' => 2,
			'beginColor' => 1,
			'vertices' =>array(145, 115, 135, 105, 122, 105, 123, 84, 138, 83, 136, 90, 158, 92, 163, 83, 177, 100, 182, 107, 165, 129, 145, 127),
			'barycentre' => array(149, 102),
			'display' => array(149, 102),
			'name' => '2'
		),
		array(
			'id' => 3,
			'beginColor' => 0,
			'vertices' =>array(145, 127, 165, 129, 181, 154, 185, 160, 156, 173, 122, 166, 123, 145, 135, 145, 145, 135),
			'barycentre' => array(151, 148),
			'display' => array(151, 148),
			'name' => '3'
		),
		array(
			'id' => 4,
			'beginColor' => 0,
			'vertices' =>array(105, 127, 105, 135, 115, 145, 123, 145, 122, 166, 100, 170, 78, 165, 68, 145, 89, 128),
			'barycentre' => array(101, 147),
			'display' => array(101, 147),
			'name' => '4'
		),
		array(
			'id' => 5,
			'beginColor' => 0,
			'vertices' =>array(105, 127, 89, 128, 65, 120, 60, 111, 65, 95, 92, 95, 101, 86, 111, 85, 123, 84, 122, 105, 115, 105, 105, 115),
			'barycentre' => array(96, 105),
			'display' => array(96, 105),
			'name' => '5'
		),
		array(
			'id' => 6,
			'beginColor' => 0,
			'vertices' =>array(62, 68, 86, 82, 106, 69, 111, 85, 101, 86, 92, 95, 65, 95, 60, 111, 46, 85),
			'barycentre' => array(81, 86),
			'display' => array(81, 86),
			'name' => '6'
		),
		array(
			'id' => 7,
			'beginColor' => 2,
			'vertices' =>array(62, 68, 60, 48, 71, 47, 69, 38, 90, 34, 107, 38, 100, 50, 106, 69, 86, 82),
			'barycentre' => array(83, 53),
			'display' => array(83, 53),
			'name' => '7'
		),
		array(
			'id' => 8,
			'beginColor' => 0,
			'vertices' =>array(122, 36, 148, 48, 138, 83, 123, 84, 111, 85, 100, 50, 107, 38, 115, 39),
			'barycentre' => array(121, 58),
			'display' => array(121, 58),
			'name' => '8'
		),
		array(
			'id' => 9,
			'beginColor' => 0,
			'vertices' =>array(158, 92, 136, 90, 148, 48, 185, 45, 198, 46, 196, 59, 178, 55, 163, 83),
			'barycentre' => array(170, 65),
			'display' => array(170, 65),
			'name' => '9'
		),
		array(
			'id' => 10,
			'beginColor' => 0,
			'vertices' =>array(217, 100, 177, 100, 163, 83, 178, 55, 187, 86, 208, 82),
			'barycentre' => array(188, 84),
			'display' => array(188, 84),
			'name' => '10'
		),
		array(
			'id' => 11,
			'beginColor' => 0,
			'vertices' =>array(182, 107, 194, 123, 209, 122, 209, 146, 181, 154, 165, 129),
			'barycentre' => array(190, 130),
			'display' => array(190, 130),
			'name' => '11'
		),
		array(
			'id' => 12,
			'beginColor' => 0,
			'vertices' =>array(122, 166, 156, 173, 185, 160, 191, 168, 195, 175, 190, 199, 172, 188, 138, 186),
			'barycentre' => array(169, 177),
			'display' => array(169, 177),
			'name' => '12'
		),
		array(
			'id' => 13,
			'beginColor' => 0,
			'vertices' =>array(172, 188, 190, 199, 199, 204, 176, 215, 143, 207, 138, 186),
			'barycentre' => array(170, 200),
			'display' => array(170, 200),
			'name' => '13'
		),
		array(
			'id' => 14,
			'beginColor' => 5,
			'vertices' =>array(103, 185, 138, 186, 143, 207, 125, 230, 101, 202),
			'barycentre' => array(122, 202),
			'display' => array(122, 202),
			'name' => '14'
		),
		array(
			'id' => 15,
			'beginColor' => 0,
			'vertices' =>array(78, 165, 100, 170, 122, 166, 138, 186, 103, 185, 101, 202, 88, 204),
			'barycentre' => array(104, 183),
			'display' => array(104, 183),
			'name' => '15'
		),
		array(
			'id' => 16,
			'beginColor' => 6,
			'vertices' =>array(70, 167, 59, 195, 35, 187, 27, 206, 48, 203, 72, 207, 88, 204, 78, 165),
			'barycentre' => array(60, 192),
			'display' => array(60, 192),
			'name' => '16'
		),
		array(
			'id' => 17,
			'beginColor' => 0,
			'vertices' =>array(35, 138, 65, 120, 89, 128, 68, 145, 78, 165, 70, 167),
			'barycentre' => array(68, 144),
			'display' => array(68, 144),
			'name' => '17'
		),
		array(
			'id' => 18,
			'beginColor' => 7,
			'vertices' =>array(0, 132, 0, 110, 25, 105, 60, 111, 65, 120, 35, 138),
			'barycentre' => array(31, 119),
			'display' => array(31, 119),
			'name' => '18'
		),
		array(
			'id' => 19,
			'beginColor' => 7,
			'vertices' =>array(0, 110, 0, 87, 22, 82, 46, 85, 60, 111, 25, 105),
			'barycentre' => array(26, 97),
			'display' => array(26, 97),
			'name' => '19'
		),
		array(
			'id' => 20,
			'beginColor' => 0,
			'vertices' =>array(0, 87, 0, 41, 35, 48, 60, 48, 62, 68, 46, 85, 22, 82),
			'barycentre' => array(32, 66),
			'display' => array(32, 66),
			'name' => '20'
		),
		array(
			'id' => 21,
			'beginColor' => 0,
			'vertices' =>array(0, 0, 0, 41, 35, 48, 60, 48, 71, 47, 69, 38, 67, 30, 73, 10, 65, 0),
			'barycentre' => array(49, 29),
			'display' => array(49, 29),
			'name' => '21'
		),
		array(
			'id' => 22,
			'beginColor' => 2,
			'vertices' =>array(65, 0, 113, 0, 135, 28, 122, 36, 115, 39, 107, 38, 90, 34, 69, 38, 67, 30, 73, 10),
			'barycentre' => array(96, 25),
			'display' => array(96, 25),
			'name' => '22'
		),
		array(
			'id' => 23,
			'beginColor' => 0,
			'vertices' =>array(113, 0, 185, 0, 180, 19, 185, 45, 148, 48, 122, 36, 135, 28),
			'barycentre' => array(153, 25),
			'display' => array(153, 25),
			'name' => '23'
		),
		array(
			'id' => 24,
			'beginColor' => 3,
			'vertices' =>array(185, 0, 250, 0, 250, 40, 230, 49, 198, 46, 185, 45, 180, 19),
			'barycentre' => array(211, 28),
			'display' => array(211, 28),
			'name' => '24'
		),
		array(
			'id' => 25,
			'beginColor' => 3,
			'vertices' =>array(250, 40, 250, 85, 235, 77, 208, 82, 187, 86, 178, 55, 196, 59, 198, 46, 230, 49),
			'barycentre' => array(215, 64),
			'display' => array(215, 64),
			'name' => '25'
		),
		array(
			'id' => 26,
			'beginColor' => 0,
			'vertices' =>array(250, 85, 250, 100, 217, 100, 208, 82, 235, 77),
			'barycentre' => array(232, 89),
			'display' => array(232, 89),
			'name' => '26'
		),
		array(
			'id' => 27,
			'beginColor' => 0,
			'vertices' =>array(250, 100, 250, 120, 209, 122, 194, 123, 182, 107, 177, 100, 217, 100),
			'barycentre' => array(211, 110),
			'display' => array(211, 110),
			'name' => '27'
		),
		array(
			'id' => 28,
			'beginColor' => 4,
			'vertices' =>array(250, 120, 250, 162, 191, 168, 185, 160, 181, 154, 209, 146, 209, 122),
			'barycentre' => array(211, 147),
			'display' => array(211, 147),
			'name' => '28'
		),
		array(
			'id' => 29,
			'beginColor' => 4,
			'vertices' =>array(250, 162, 250, 202, 199, 204, 190, 199, 195, 175, 191, 168),
			'barycentre' => array(213, 185),
			'display' => array(213, 185),
			'name' => '29'
		),
		array(
			'id' => 30,
			'beginColor' => 0,
			'vertices' =>array(250, 202, 250, 250, 181, 250, 176, 215, 199, 204),
			'barycentre' => array(211, 224),
			'display' => array(211, 224),
			'name' => '30'
		),
		array(
			'id' => 31,
			'beginColor' => 5,
			'vertices' =>array(181, 250, 123, 250, 125, 230, 143, 207, 176, 215),
			'barycentre' => array(150, 230),
			'display' => array(150, 230),
			'name' => '31'
		),
		array(
			'id' => 32,
			'beginColor' => 0,
			'vertices' =>array(123, 250, 73, 250, 72, 207, 88, 204, 101, 202, 125, 230),
			'barycentre' => array(97, 224),
			'display' => array(97, 224),
			'name' => '32'
		),
		array(
			'id' => 33,
			'beginColor' => 0,
			'vertices' =>array(73, 250, 0, 250, 0, 210, 27, 206, 48, 203, 72, 207),
			'barycentre' => array(37, 221),
			'display' => array(37, 221),
			'name' => '33'
		),
		array(
			'id' => 34,
			'beginColor' => 6,
			'vertices' =>array(0, 180, 70, 167, 59, 195, 35, 187, 27, 206, 0, 210),
			'barycentre' => array(32, 191),
			'display' => array(32, 191),
			'name' => '34'
		),
		array(
			'id' => 35,
			'beginColor' => 0,
			'vertices' =>array(0, 132, 35, 138, 70, 167, 0, 180),
			'barycentre' => array(26, 154),
			'display' => array(26, 154),
			'name' => '35'
		)
	);

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

			echo 'array(' . "\r\n";
			echo '	\'id\' => ' . $k . ',' . "\r\n";
			echo '	\'beginColor\' => 0,' . "\r\n";
			echo '	\'vertices\' =>array(' . implode(', ', $sector['vertices']) . '),' . "\r\n";
			echo '	\'barycentre\' => array(' . $gx . ', ' . $gy . '),' . "\r\n";
			echo '	\'display\' => array(' . $gx . ', ' . $gy . '),' . "\r\n";
			echo '),' . "\r\n";
		
			$k++;
		}
		echo '</pre>';
	}

	public static $systems = array(
		array(
			'id' => 1,
			'name' => 'ruine',
			'placesPropotion' => array(0, 0, 80, 10, 0, 10),
			'nbrPlaces' => array(1, 5)
		),
		array(
			'id' => 2,
			'name' => 'nébuleuse',
			'placesPropotion' => array(5, 5, 10, 75, 0, 5),
			'nbrPlaces' => array(2, 8)
		),
		array(
			'id' => 3,
			'name' => 'géante bleue',
			'placesPropotion' => array(40, 25, 10, 0, 20, 5),
			'nbrPlaces' => array(12, 18)
		),
		array(
			'id' => 4,
			'name' => 'naine jaune',
			'placesPropotion' => array(55, 20, 5, 0, 15, 5),
			'nbrPlaces' => array(6, 12)
		),
		array(
			'id' => 5,
			'name' => 'naine rouge',
			'placesPropotion' => array(70, 10, 5, 0, 10, 5),
			'nbrPlaces' => array(2, 6)
		)
	);

	public static $places = array(
		array(
			'id' => 1,
			'name' => 'planète tellurique',
			'resources' => array(45, 55),
			'history' => array(20, 40),
		),
		array(
			'id' => 2,
			'name' => 'planète gazeuse',
			'resources' => array(60, 80),
			'history' => array(0, 10),
		),
		array(
			'id' => 3,
			'name' => 'ruine',
			'resources' => array(30, 40),
			'history' => array(90, 100),
		),
		array(
			'id' => 4,
			'name' => 'poches de gaz',
			'resources' => array(80, 100),
			'history' => array(0, 25),
		),
		array(
			'id' => 5,
			'name' => 'ceinture d\'astéroides',
			'resources' => array(80, 100),
			'history' => array(10, 25),
		),
		array(
			'id' => 6,
			'name' => 'lieu vide',
			'resources' => array(0, 0),
			'history' => array(0, 0),
		)
	);

	# display params
	public static $scale = 20;
}
?>