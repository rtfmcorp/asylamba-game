<?php

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');
$systemManager = $this->getContainer()->get('gaia.system_manager');
$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$sectorManager->load();

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
	}
# par system
} elseif ($request->query->has('system')) {
	$_SYS_MAP = $systemManager->getCurrentSession();
	$systemManager->newSession();
	$systemManager->load(array('id' => $request->query->get('system')));

	if ($systemManager->size() == 1) {
		$x = $systemManager->get(0)->xPosition;
		$y = $systemManager->get(0)->yPosition;
		$systemId = $request->query->get('system');
	}
	
	$systemManager->changeSession($_SYS_MAP);
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

if ($systemId != 0) {
	$S_SYS1 = $systemManager->getCurrentSession();
	$systemManager->newSession();
	$systemManager->load(array('id' => $systemId));

	if ($systemManager->size() == 1) {
		# objet système
		$system = $systemManager->get();

		# objet place
		$places = $placeManager->getSystemPlaces($system);

		$noAJAX = TRUE;

		# inclusion du "composant"
		echo '<div id="action-box" style="bottom: 0px;">';
			include PAGES . 'desktop/mapElement/actionbox.php';
		echo '</div>';
	}
	$systemManager->changeSession($S_SYS1);
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