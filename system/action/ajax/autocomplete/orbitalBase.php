<?php

$placeManager = $this->getContainer()->get('gaia.place_manager');

$S_PLM1 = $placeManager->newSession(FALSE);

$placeManager->search($_GET['q'], array('pl.id', 'DESC'), array(0, 20));

if ($placeManager->size() != 0) {
	for ($i = 0; $i < $placeManager->size(); $i++) {
		$place = $placeManager->get($i);

		echo '<img class="img" src="' . MEDIA . 'avatar/small/' . $place->playerAvatar . '.png" alt="' . $place->playerName . '" /> ';
		echo '<span class="value-2">' . $place->playerName . '</span>';
		echo '<span class="value-1"><span class="ac_value" data-id="' . $place->getId() . '">' . $place->getBaseName() . '</span> (secteur ' . $place->getRSector() . ')</span>';
		echo "\n";
	}
}

$placeManager->changeSession($S_PLM1);