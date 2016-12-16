<?php

$request = $this->getContainer()->get('app.request');
$systemManager = $this->getContainer()->get('gaia.system_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');

if ($request->query->has('systemid')) {
	$systemId = $request->query->get('systemid');
} else if ($request->request->has('systemid')) {
	$systemId = $request->request->get('systemid');
} else {
	$systemId = FALSE;
}

$S_SYS1 = $systemManager->getCurrentSession();
$systemManager->newSession();
$systemManager->load(array('id' => $systemId));

if ($systemManager->size() == 1) {
	# objet système
	$system = $systemManager->get();

	# objet place
	$places = array();
	$S_PLM1 = $placeManager->getCurrentSession();
	$placeManager->newSession();
	$placeManager->load(array('rSystem' => $systemId), array('position'));
	for ($i = 0; $i < $placeManager->size(); $i++) {
		$places[] = $placeManager->get($i);
	}
	$placeManager->changeSession($S_PLM1);

	# inclusion du "composant"
	include PAGES . 'desktop/mapElement/actionbox.php';
} else {
	return FALSE;
}

$systemManager->changeSession($S_SYS1);
