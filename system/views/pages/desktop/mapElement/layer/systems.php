<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');

echo '<div id="systems">';
	$qr = $this->getContainer()->get('database')->prepare('SELECT * FROM system');
	$qr->execute();
	$aw = $qr->fetchAll();

	# own bases
	$basesId = array();
	for ($i = 0; $i < $orbitalBaseManager->size(); $i++) { 
		$basesId[]  = $orbitalBaseManager->get($i)->getSystem();
	}

	foreach ($aw as $system) {
		$owner = (in_array($system['id'], $basesId)) ? 'class="own"' : '';
		echo '<a ';
			echo 'href="#" ';
			echo 'class="loadSystem ' . $systemId . ' ' . ($system['id'] == $systemId ? 'active' : NULL) . '" ';
			echo 'data-system-id="' . $system['id'] . '" ';
			echo 'data-x-position="' . $system['xPosition'] . '" data-y-position="' . $system['yPosition'] . '" ';
			echo 'style="top: ' . ($system['yPosition'] * $galaxyConfiguration->scale - 10) . 'px; left: ' . ($system['xPosition'] * $galaxyConfiguration->scale - 10) . 'px">';
			echo '<img src="' . MEDIA . 'map/systems/t' . $system['typeOfSystem'] . 'c' . $system['rColor'] . '.png" ' . $owner . ' />';
		echo '</a>';
	}

	for ($i = 0; $i < $sectorManager->size(); $i++) {
		$sector = $sectorManager->get($i);

		echo '<span ';
			echo 'class="sector-number color' . $sector->getRColor() . ' sh" ';
			echo 'data-target="sector-info-' . $sector->getId() . '" ';
			echo 'style="left: ' . $sector->getXBarycentric() * $galaxyConfiguration->scale . 'px; top: ' . $sector->getYBarycentric() * $galaxyConfiguration->scale . 'px">';
			echo ($i + 1);
		echo '</span>';

		echo '<div id="sector-info-' . ($i + 1) . '" class="sector-info color' . $sector->getRColor() . '" style="left: ' . ($sector->getXBarycentric() * $galaxyConfiguration->scale + 55) . 'px; top: ' . ($sector->getYBarycentric() * $galaxyConfiguration->scale - 10) . 'px">';
			echo '<h2>' . $sector->getName() . '</h2>';
			echo '<p><a href="#">+</a> ';
				if ($sector->getRColor() != 0) {
					echo 'Revendiqué par ' . ColorResource::getInfo($sector->getRColor(), 'popularName') . ' | ' . $sector->getTax() . '% de taxe' . ' | rapporte ' . $sector->points . Format::addPlural($sector->points, ' points', ' point');
				} else {
					echo 'Non revendiqué | Aucune taxe' . ' | rapporte ' . $sector->points . Format::addPlural($sector->points, ' points', ' point') . '</p>';
				}
			echo '</p>';
		echo '</div>';
	}
	
echo '</div>';
