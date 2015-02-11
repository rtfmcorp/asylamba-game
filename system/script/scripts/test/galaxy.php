<?php
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
$i = 1;
foreach ($abilities as $k => $v) {
	if ($i < 3) {
		$max = $pointsTot - ($i * 10);
		$max = $max < 10 ? 10 : $max;

		$points = rand(10, $max);
		$abilities[$k] = $points;
		$pointsTot -= $points;
	} else {
		$abilities[$k] = $pointsTot;
	}

	$i++;
}


var_dump('points total après');
var_dump(array_sum($abilities));

var_dump($abilities);
?>