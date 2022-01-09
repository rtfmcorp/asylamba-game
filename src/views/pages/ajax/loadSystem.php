<?php

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$systemManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\SystemManager::class);
$placeManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\PlaceManager::class);

if ($request->query->has('systemid')) {
	$systemId = $request->query->get('systemid');
} else if ($request->request->has('systemid')) {
	$systemId = $request->request->get('systemid');
} else {
	$systemId = FALSE;
}

if (($system = $systemManager->get($systemId)) !== null) {
	# objet place
	$places = $placeManager->getSystemPlaces($system);

	# inclusion du "composant"
	include $container->getParameter('pages') . 'desktop/mapElement/actionbox.php';
} else {
	return FALSE;
}
