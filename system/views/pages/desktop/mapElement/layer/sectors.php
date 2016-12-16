<?php

$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');

echo '<div id="sectors">';
	echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
		for ($i = 0; $i < $sectorManager->size(); $i++) {
			echo '<polygon ';
				echo 'class="ally' . $sectorManager->get($i)->getRColor() . '" ';
				echo 'points="' . $galaxyConfiguration->getSectorCoord($sectorManager->get($i)->getId(), $galaxyConfiguration->scale, 0) . '" ';
				echo 'data-x-brc="' . $sectorManager->get($i)->getXBarycentric() . '" ';
				echo 'data-y-brc="' . $sectorManager->get($i)->getYBarycentric() . '" ';
			echo '/>';
		}
	echo '</svg>';
echo '</div>';
