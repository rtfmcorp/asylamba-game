<?php

use App\Classes\Container\Params;
use App\Classes\Library\Utils;
use App\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$commanderManager = $this->getContainer()->get(\App\Modules\Ares\Manager\CommanderManager::class);
$placeManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\PlaceManager::class);
$galaxyConfiguration = $this->getContainer()->get(\App\Modules\Gaia\Galaxy\GalaxyConfiguration::class);

$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::MOVING]);

echo '<div id="fleet-movements" ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETOUT, Params::$params[Params::SHOW_MAP_FLEETOUT]) ? NULL : 'style="display:none;"') . '>';
	echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
			foreach ($commanders as $commander) {
				if ($commander->rDestinationPlace !== NULL) {
					$startPlace = $placeManager->get($commander->rStartPlace);
					$destinationPlace = $placeManager->get($commander->rDestinationPlace);
					$x1 = $startPlace->getXSystem() * $galaxyConfiguration->scale;
					$x2 = $destinationPlace->getXSystem() * $galaxyConfiguration->scale;
					$y1 = $startPlace->getYSystem() * $galaxyConfiguration->scale;
					$y2 = $destinationPlace->getYSystem() * $galaxyConfiguration->scale;
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
