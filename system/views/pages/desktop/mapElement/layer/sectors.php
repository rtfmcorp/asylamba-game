<?php

$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');

echo '<div id="sectors">';
	echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
		foreach ($sectors as $sector) {
			echo '<polygon ';
				echo 'class="ally' . $sector->getRColor() . '" ';
				echo 'points="' . $galaxyConfiguration->getSectorCoord($sector->getId(), $galaxyConfiguration->scale, 0) . '" ';
				echo 'data-x-brc="' . $sector->getXBarycentric() . '" ';
				echo 'data-y-brc="' . $sector->getYBarycentric() . '" ';
			echo '/>';
		}
	echo '</svg>';
echo '</div>';
