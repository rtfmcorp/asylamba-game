<?php

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$placeManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\PlaceManager::class);
$request = $this->getContainer()->get('app.request');

if ($request->query->has('q')) {
	$places = $placeManager->search($request->query->get('q'), array('pl.id', 'DESC'), array(0, 20));
	
	foreach ($places as $place) {
		echo '<img class="img" src="' . $mediaPath . 'avatar/small/' . $place->playerAvatar . '.png" alt="' . $place->playerName . '" /> ';
		echo '<span class="value-2">' . $place->playerName . '</span>';
		echo '<span class="value-1"><span class="ac_value" data-id="' . $place->getId() . '">' . $place->getBaseName() . '</span> (secteur ' . $place->getRSector() . ')</span>';
		echo "\n";
	}
}
