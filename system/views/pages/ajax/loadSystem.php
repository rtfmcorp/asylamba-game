<?php

$request = $this->getContainer()->get('app.request');
$systemManager = $this->getContainer()->get('gaia.system_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');

if ($request->query->has('systemid')) {
    $systemId = $request->query->get('systemid');
} elseif ($request->request->has('systemid')) {
    $systemId = $request->request->get('systemid');
} else {
    $systemId = false;
}

if (($system = $systemManager->get($systemId)) !== null) {
    # objet place
    $places = $placeManager->getSystemPlaces($system);

    # inclusion du "composant"
    include PAGES . 'desktop/mapElement/actionbox.php';
} else {
    return false;
}
