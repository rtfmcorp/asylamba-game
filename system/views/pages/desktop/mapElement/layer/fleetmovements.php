<?php

use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');

$S_COM_MAPLAYER = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.statement' => Commander::MOVING, 'c.rPlayer' => $session->get('playerId')));

$placesId = array(0);
for ($i = 0; $i < $commanderManager->size(); $i++) {
	$placesId[] = $commanderManager->get($i)->rStartPlace;
	$placesId[] = $commanderManager->get($i)->rDestinationPlace;
}

$S_PLM_MAPLAYER = $placeManager->getCurrentSession();
$placeManager->newSession();
$placeManager->load(array('id' => $placesId));

echo '<div id="fleet-movements" ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETOUT, Params::SHOW_MAP_FLEETOUT) ? NULL : 'style="display:none;"') . '>';
	echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
			for ($i = 0; $i < $commanderManager->size(); $i++) {
				$commander = $commanderManager->get($i);

				if ($commander->rDestinationPlace !== NULL) {
					$x1 = $placeManager->getById($commander->rStartPlace)->getXSystem() * $galaxyConfiguration->scale;
					$x2 = $placeManager->getById($commander->rDestinationPlace)->getXSystem() * $galaxyConfiguration->scale;
					$y1 = $placeManager->getById($commander->rStartPlace)->getYSystem() * $galaxyConfiguration->scale;
					$y2 = $placeManager->getById($commander->rDestinationPlace)->getYSystem() * $galaxyConfiguration->scale;
					list($x3, $y3) = $commanderManager->getPosition($commander, $x1, $y1, $x2, $y2);
					$rt = Utils::interval($commander->dArrival, Utils::now(), 's');

					echo '<line ' . ($commander->travelType == Commander::BACK ? 'class="back"' : NULL) . ' x1="' . $x1 . '" x2="' . $x2 . '" y1="' . $y1 . '" y2="' . $y2 . '" />';
					echo '<circle cx="0" cy="0" r="3">';
						echo '<animate attributeName="cx" attributeType="XML" fill="freeze" from="' . $x3 . '" to="' . $x2 . '" begin="0s" dur="' . $rt . 's"/>';
						echo '<animate attributeName="cy" attributeType="XML" fill="freeze" from="' . $y3 . '" to="' . $y2 . '" begin="0s" dur="' . $rt . 's"/>';
					echo '</circle>';
				}
			}
	echo '</svg>';
echo '</div>';

$placeManager->changeSession($S_PLM_MAPLAYER);
$commanderManager->changeSession($S_COM_MAPLAYER);
