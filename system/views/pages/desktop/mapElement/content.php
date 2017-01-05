<?php

use Asylamba\Classes\Container\Params;

$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');

$rate = 400 / $galaxyConfiguration->galaxy['size'];
echo '<div id="map-content" ' . ($request->cookies->get('p' . Params::SHOW_MAP_MINIMAP, Params::SHOW_MAP_MINIMAP) ? NULL : 'style="display:none;"') . '>';
	echo '<div class="mini-map">';
		echo '<svg class="sectors" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">';
			for ($i = 0; $i < $sectorManager->size(); $i++) {
				echo '<polygon ';
					echo 'class="ally' . $sectorManager->get($i)->getRColor() . ' moveTo" ';
					echo 'points="' . $galaxyConfiguration->getSectorCoord($sectorManager->get($i)->getId(), $rate, 0) . '" ';
					echo 'data-x-position="' . $sectorManager->get($i)->getXBarycentric() . '" data-y-position="' . $sectorManager->get($i)->getYBarycentric() . '" ';
				echo '/>';
			}
		echo '</svg>';
		echo '<div class="number">';
			for ($i = 0; $i < $sectorManager->size(); $i++) {
				echo '<span style="top: ' . ($galaxyConfiguration->sectors[$i]['display'][1] * $rate / 1.35) . 'px; left: ' . ($galaxyConfiguration->sectors[$i]['display'][0] * $rate / 1.35) . 'px;">';
					echo $sectorManager->get($i)->getId();
				echo '</span>';
			}
		echo '</div>';
		echo '<svg class="bases" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">';
			for ($i = 0; $i < $orbitalBaseManager->size(); $i++) {
				$base = $orbitalBaseManager->get($i);
				echo '<circle cx="' . ($base->getXSystem() * $rate) . '" cy="' . ($base->getYSystem() * $rate) . '" r="4" />';
			}
		echo '</svg>';
		echo '<div class="viewport"></div>';
	echo '</div>';
echo '</div>';
