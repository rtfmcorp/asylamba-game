<?php


$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');

$sectorManager->load();
$rate = 750 / $galaxyConfiguration->galaxy['size'];

echo '<div class="component size2">';
	echo '<div class="head skin-2">';
		echo '<h2>Carte tactique</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tactical-map">';
				echo '<svg class="sectors" viewBox="0, 0, 750, 750" xmlns="http://www.w3.org/2000/svg">';
					for ($i = 0; $i < $sectorManager->size(); $i++) {
						$s = $sectorManager->get($i);
						echo '<polygon data-id="' . $s->getId() . '"';
							echo 'class="ally' . $s->getRColor() . ' ' . ($s->getRColor() != 0 ? 'enabled' : 'disabled') . '" ';
							echo 'points="' . $galaxyConfiguration->getSectorCoord($s->getId(), $rate, 0) . '" ';
						echo '/>';
					}
				echo '</svg>';
				echo '<div class="number">';
					for ($i = 0; $i < $sectorManager->size(); $i++) {
						$s = $sectorManager->get($i);
						echo '<span id="sector' . $s->getId() . '" class="ally' . $s->getRColor() . '" style="top: ' . ($galaxyConfiguration->sectors[$i]['display'][1] * $rate / 1.35) . 'px; left: ' . ($galaxyConfiguration->sectors[$i]['display'][0] * $rate / 1.35) . 'px;">';
							echo $s->getId();
						echo '</span>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
