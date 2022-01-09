<?php

$container = $this->getContainer();
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get(\App\Modules\Athena\Manager\OrbitalBaseManager::class);
$placeManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\PlaceManager::class);
$sectorManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\SectorManager::class);
$systemManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\SystemManager::class);
$galaxyConfiguration = $this->getContainer()->get(\App\Modules\Gaia\Galaxy\GalaxyConfiguration::class);
$sectors = $sectorManager->getAll();

$playerBases = $orbitalBaseManager->getPlayerBases($session->get('playerId'));
$defaultBase = $orbitalBaseManager->get($session->get('playerParams')->get('base'));

# map default position
$x = $defaultBase->getXSystem();
$y = $defaultBase->getYSystem();
$systemId = 0;

# other default location
# par place
if ($request->query->has('place')) {
	if (($place = $placeManager->get($request->query->get('place'))) !== null) {
		$x = $place->getXSystem();
		$y = $place->getYSystem();
		$systemId = $place->getRSystem();
		$system = $systemManager->get($systemId);
	}
# par system
} elseif ($request->query->has('systemid')) {
	if (($system = $systemManager->get($request->query->get('systemid'))) !== null) {
		$x = $system->xPosition;
		$y = $system->yPosition;
		$systemId = $request->query->get('systemid');
	}
# par coordonnée
} elseif ($request->query->has('x') && $request->query->has('y')) {
	$x = $request->query->get('x');
	$y = $request->query->get('y');
}

# control include
include 'mapElement/option.php';
include 'mapElement/content.php';
include 'mapElement/commanders.php';
include 'mapElement/coordbox.php';

if (!empty($system)) {
	# objet place
	$places = $placeManager->getSystemPlaces($system);

	$noAJAX = TRUE;

	# inclusion du "composant"
	echo '<div id="action-box" style="bottom: 0px;">';
		include $container->getParameter('pages') . 'desktop/mapElement/actionbox.php';
	echo '</div>';
} else {
	echo '<div id="action-box"></div>';
}

# map sytems
echo '<div id="map" ';
	echo 'data-begin-x-position="' . $x . '" ';
	echo 'data-begin-y-position="' . $y . '" ';
	echo 'data-related-place="' . $defaultBase->getId() . '"';
	echo 'data-map-size="' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '"';
	echo 'data-map-ratio="' . $galaxyConfiguration->scale . '"';
	echo 'style="width: ' . (($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size'])) . 'px; height: ' . (($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size'])) . 'px;"';
echo '>';
	include 'mapElement/layer/background.php';

	include 'mapElement/layer/sectors.php';

	include 'mapElement/layer/spying.php';
	include 'mapElement/layer/ownbase.php';
	include 'mapElement/layer/commercialroutes.php';
	include 'mapElement/layer/fleetmovements.php';
	include 'mapElement/layer/attacks.php';

	include 'mapElement/layer/systems.php';
	include 'mapElement/layer/map-info.php';
echo '</div>';
