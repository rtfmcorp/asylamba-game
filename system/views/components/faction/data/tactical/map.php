<?php

use Asylamba\Modules\Gaia\Manager\SectorManager;

$sm = new SectorManager();
$sm->load();
$rate = 750 / GalaxyConfiguration::$galaxy['size'];

echo '<div class="component size2">';
	echo '<div class="head skin-2">';
		echo '<h2>Carte tactique</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tactical-map">';
				echo '<svg class="sectors" viewBox="0, 0, 750, 750" xmlns="http://www.w3.org/2000/svg">';
					for ($i = 0; $i < $sm->size(); $i++) {
						$s = $sm->get($i);
						echo '<polygon data-id="' . $s->getId() . '"';
							echo 'class="ally' . $s->getRColor() . ' ' . ($s->getRColor() != 0 ? 'enabled' : 'disabled') . '" ';
							echo 'points="' . GalaxyConfiguration::getSectorCoord($s->getId(), $rate, 0) . '" ';
						echo '/>';
					}
				echo '</svg>';
				echo '<div class="number">';
					for ($i = 0; $i < $sm->size(); $i++) {
						$s = $sm->get($i);
						echo '<span id="sector' . $s->getId() . '" class="ally' . $s->getRColor() . '" style="top: ' . (GalaxyConfiguration::$sectors[$i]['display'][1] * $rate / 1.35) . 'px; left: ' . (GalaxyConfiguration::$sectors[$i]['display'][0] * $rate / 1.35) . 'px;">';
							echo $s->getId();
						echo '</span>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
