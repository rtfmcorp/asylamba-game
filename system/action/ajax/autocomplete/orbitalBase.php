<?php

$placeManager = $this->getContainer()->get('gaia.place_manager');
$request = $this->getContainer()->get('app.request');

if ($request->query->has('q')) {
    $places = $placeManager->search($request->query->get('q'), array('pl.id', 'DESC'), array(0, 20));
    
    foreach ($places as $place) {
        echo '<img class="img" src="' . MEDIA . 'avatar/small/' . $place->playerAvatar . '.png" alt="' . $place->playerName . '" /> ';
        echo '<span class="value-2">' . $place->playerName . '</span>';
        echo '<span class="value-1"><span class="ac_value" data-id="' . $place->getId() . '">' . $place->getBaseName() . '</span> (secteur ' . $place->getRSector() . ')</span>';
        echo "\n";
    }
}
