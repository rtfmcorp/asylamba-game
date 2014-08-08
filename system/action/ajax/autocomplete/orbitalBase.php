<?php
include_once GAIA;
# warning : we want an orbital base but we search on places
$S_PLM1 = ASM::$plm->newSession(false);

$name = $_GET['q'];
$name = htmlspecialchars($name);
$p = new Parser();
$name = $p->protect($name);
$name = strtr($name, "'", "\'");
ASM::$plm->search($name);

if (ASM::$plm->size() != 0) {
	for ($i = 0; $i < ASM::$plm->size(); $i++) {
		$place = ASM::$plm->get($i);

		echo '<img class="img" src="' . MEDIA . 'avatar/small/' . $place->playerAvatar . '.png" alt="' . $place->playerName . '" /> ';
		echo '<span>' . $place->playerName . '</span>';
		if ($place->typeOfBase == 4) {
			echo '<span class="value-2">Base orbitale</span>';
		} else {
			echo '<span class="value-2">Vaisseau-mère</span>';
		}
		echo '<span class="value-1"><span class="ac_value">' . $place->baseName . '</span></span>';
		echo "\n";
	}
}
ASM::$plm->changeSession($S_PLM1);
?>