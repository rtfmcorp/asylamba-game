<?php
# udpate ships in squadron

# int base 			ref base id
# int commander		ref commander id
# int squadron 		ref squadron id

# string newSquadron	liste de vaisseaux séparé par un _

use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Ares\Model\Commander;

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

if (
	($baseID = $request->query->get('base')) === null	||
	($commanderID = $request->query->get('commander')) === null	||
	($squadronID = $request->query->get('squadron')) === null	||
	($newSquadron = $request->query->get('army')) === null	||
	!in_array($baseID, $verif)
) {
	throw new FormException('Pas assez d\'informations pour assigner un vaisseau.');	
}

$newSquadron = explode('_', $newSquadron);
$newSquadron = array_map(function($el) {
	return $el < 0 ? 0 : (int)$el;
}, $newSquadron);

if (count($newSquadron) !== 12) {
	throw new FormException('Pas assez d\'informations pour assigner un vaisseau.');
}

$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');

# chargement du commandant
$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commanderID, 'c.rBase' => $baseID, 'c.statement' => [Commander::AFFECTED]));

if (($base = $orbitalBaseManager->get($baseID)) === null || $commanderManager->size() !== 1) {
	throw new ErrorException('Erreur dans les références du commandant ou de la base.');
}

$commander = $commanderManager->get();
$squadron = $commander->getSquadron($squadronID);

if ($squadron === false) {
	throw new ErrorException('Erreur dans les références du commandant ou de la base.');
}

$squadronSHIP = $squadron->arrayOfShips;
$baseSHIP = $base->shipStorage;

foreach ($newSquadron as $i => $v) {
	$baseSHIP[$i] -= ($v - $squadronSHIP[$i]);
	$squadronSHIP[$i] = $v;
}

# token de vérification
$baseOK = TRUE;
$squadronOK = TRUE;
$totalPEV = 0;
# vérif shipStorage (pas de nombre négatif)
foreach ($baseSHIP as $i => $v) {
	if ($v < 0) {
		$baseOK = FALSE;
		break;
	}
}

# vérif de squadron (pas plus de 100 PEV, pas de nombre négatif)
foreach ($squadronSHIP as $i => $v) {
	$totalPEV += $v * ShipResource::getInfo($i, 'pev');
	if ($v < 0) {
		$squadronOK = FALSE;
		break;
	}
}

if (!$baseOK || !$squadronOK || $totalPEV > 100) {
	throw new ErrorException('Erreur dans la répartition des vaisseaux.');
}
# tutorial
if ($session->get('playerInfo')->get('stepDone') === false && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::FILL_SQUADRON) {
	$tutorialHelper->setStepDone();
}

$base->shipStorage = $baseSHIP;
$commander->getSquadron($squadronID)->arrayOfShips = $squadronSHIP;

$commanderManager->changeSession($S_COM1);