<?php

use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');

# chargement des commandants attaquants
$attackingCommanders = array_merge(
	$commanderManager->getIncomingAttacks($session->get('playerId')),
	$commanderManager->getOutcomingAttacks($session->get('playerId'))
);

echo '<div id="attacks" ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETIN, Params::SHOW_MAP_FLEETIN) ? NULL : 'style="display:none;"') . '>';
	echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
			foreach ($attackingCommanders as $commander) {

				if ($commander->travelType != Commander::BACK) {
					$startPlace = $placeManager->get($commander->rStartPlace);
					$destinationPlace = $placeManager->get($commander->rDestinationPlace);
					$x1 = $startPlace->getXSystem() * $galaxyConfiguration->scale;
					$x2 = $destinationPlace->getXSystem() * $galaxyConfiguration->scale;
					$y1 = $startPlace->getYSystem() * $galaxyConfiguration->scale;
					$y2 = $destinationPlace->getYSystem() * $galaxyConfiguration->scale;
					list($x3, $y3) = $commanderManager->getPosition($commander, $x1, $y1, $x2, $y2);
					$rt = Utils::interval($commander->dArrival, Utils::now(), 's');

					echo '<line class="color' . $commander->playerColor . '" x1="' . $x1 . '" x2="' . $x2 . '" y1="' . $y1 . '" y2="' . $y2 . '" />';
					echo '<circle class="color' . $commander->playerColor . '" cx="0" cy="0" r="3">';
						echo '<animate attributeName="cx" attributeType="XML" fill="freeze" from="' . $x3 . '" to="' . $x2 . '" begin="0s" dur="' . $rt . 's"/>';
						echo '<animate attributeName="cy" attributeType="XML" fill="freeze" from="' . $y3 . '" to="' . $y2 . '" begin="0s" dur="' . $rt . 's"/>';
					echo '</circle>';
				}
			}
	echo '</svg>';
echo '</div>';