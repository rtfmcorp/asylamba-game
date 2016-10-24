<?php

use Asylamba\Classes\Worker\ASM;

$S_PLM1 = ASM::$plm->newSession(FALSE);

ASM::$plm->search($_GET['q'], array('pl.id', 'DESC'), array(0, 20));

if (ASM::$plm->size() != 0) {
	for ($i = 0; $i < ASM::$plm->size(); $i++) {
		$place = ASM::$plm->get($i);

		echo '<img class="img" src="' . MEDIA . 'avatar/small/' . $place->playerAvatar . '.png" alt="' . $place->playerName . '" /> ';
		echo '<span class="value-2">' . $place->playerName . '</span>';
		echo '<span class="value-1"><span class="ac_value" data-id="' . $place->getId() . '">' . $place->getBaseName() . '</span> (secteur ' . $place->getRSector() . ')</span>';
		echo "\n";
	}
}

ASM::$plm->changeSession($S_PLM1);