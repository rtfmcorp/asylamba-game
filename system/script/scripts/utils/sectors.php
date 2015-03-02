<?php
# find des trucs

$angle = 0.8976;
$bridge = 7;

for ($i = 0; $i < $bridge; $i++) {
	$xA = (cos($i * $angle) * ((110 / 125) * 100)) + 125;
	$yA = (sin($i * $angle) * ((110 / 125) * 100)) + 125;

	$xB = (cos($i * $angle) * ((65 / 125) * 100)) + 125;
	$yB = (sin($i * $angle) * ((65 / 125) * 100)) + 125;

	echo '[[' . round($xB) . ',  ' . round($yB) . '], [' . round($xA) . ',  ' . round($yA) . '], 8],' . '<br />';
}