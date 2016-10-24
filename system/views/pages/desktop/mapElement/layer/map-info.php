<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;

echo '<div id="map-info">';
	echo '<h2>Légende</h2>';

	echo '<h3>Type de système</h3>';
	echo '<ul>';
		echo '<li>Cimetière Spatial <img src="' . MEDIA . 'map/systems/t1c0.png" /></li>';
		echo '<li>Nébuleuse <img src="' . MEDIA . 'map/systems/t2c0.png" /></li>';
		echo '<li>Géante Bleue <img src="' . MEDIA . 'map/systems/t3c0.png" /></li>';
		echo '<li>Naine Jaune <img src="' . MEDIA . 'map/systems/t4c0.png" /></li>';
		echo '<li>Naine Rouge <img src="' . MEDIA . 'map/systems/t5c0.png" /></li>';
	echo '</ul>';

	echo '<h3>Revendication</h3>';
	echo '<ul>';
		echo '<li>Non revendiquée <img src="' . MEDIA . 'map/systems/t4c0.png" /></li>';
		
		//foreach ([4, 8, 10, 11, 12] as $faction) {
		foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] as $faction) {
			echo '<li>' . ColorResource::getInfo($faction, 'officialName') . ' <img src="' . MEDIA . 'map/systems/t4c' . $faction . '.png" /></li>';
		}
	echo '</ul>';
echo '</div>';