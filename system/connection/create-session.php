<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Container\ArrayList;
use Asylamba\Modules\Ares\Model\Commander;

$session = $this->getContainer()->get('app.session');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$buildingQueueManager = $this->getContainer()->get('athena.building_queue_manager');
$shipQueueManager = $this->getContainer()->get('athena.ship_queue_manager');
$technologyQueueManager = $this->getContainer()->get('promethee.technology_queue_manager');

# création des tableaux de données dans le contrôler
$session->initPlayerInfo();
$session->initPlayerBase();
$session->initPlayerBonus();

# remplissage des données du joueur
$session->add('playerId', $player->getId());

$session->get('playerInfo')->add('color', $player->getRColor());
$session->get('playerInfo')->add('name', $player->getName());
$session->get('playerInfo')->add('avatar', $player->getAvatar());
$session->get('playerInfo')->add('credit', $player->getCredit());
$session->get('playerInfo')->add('experience', $player->getExperience());
$session->get('playerInfo')->add('level', $player->getLevel());
$session->get('playerInfo')->add('stepTutorial', $player->stepTutorial);
$session->get('playerInfo')->add('stepDone', $player->stepDone);
$session->get('playerInfo')->add('status', $player->status);
$session->get('playerInfo')->add('premium', $player->premium);

if (Utils::isAdmin($player->getBind())) {
	$session->get('playerInfo')->add('admin', TRUE);
} else {
	$session->get('playerInfo')->add('admin', FALSE);
}

$playerBases = $orbitalBaseManager->getPlayerBases($player->getId());
foreach ($playerBases as $base) {
	$session->addBase(
		'ob', $base->getId(), 
		$base->getName(), 
		$base->getSector(), 
		$base->getSystem(), 
		'1-' . Game::getSizeOfPlanet($base->getPlanetPopulation()),
		$base->typeOfBase
	);
}
# remplissage des bonus
$playerBonusManager = $this->getContainer()->get('zeus.player_bonus_manager');
$bonus = $playerBonusManager->getBonusByPlayer($player->getId());
$playerBonusManager->initialize($bonus);

# création des paramètres utilisateur
$session->add('playerParams', new ArrayList());

# remplissage des paramètres utilisateur
$session->get('playerParams')->add('base', $session->get('playerBase')->get('ob')->get(0)->get('id'));

# création des tableaux de données dans le contrôleur
$session->initPlayerEvent();

# remplissage des events
$playerBases = $orbitalBaseManager->getPlayerBases($session->get('playerId'));
$now = Utils::now();
foreach ($playerBases as $orbitalBase) { 
	$baseId = $orbitalBase->getRPlace();
	# check the building queues
	$S_BQM1 = $buildingQueueManager->getCurrentSession();
	$buildingQueueManager->newSession();
	$buildingQueueManager->load(array('rOrbitalBase' => $baseId), array('dEnd'));
	for ($j = 0; $j < $buildingQueueManager->size(); $j++) { 
		$date = $buildingQueueManager->get($j)->dEnd;
		$session->get('playerEvent')->add($date, EVENT_BASE, $baseId);
	}
	$buildingQueueManager->changeSession($S_BQM1);

	# check the ship queues of dock 1
	$S_SQM1 = $shipQueueManager->getCurrentSession();
	$shipQueueManager->newSession();
	$shipQueueManager->load(array('rOrbitalBase' => $baseId, 'dockType' => 1), array('dEnd'));
	for ($j = 0; $j < $shipQueueManager->size(); $j++) { 
		$date = $shipQueueManager->get($j)->dEnd;
		$session->get('playerEvent')->add($date, EVENT_BASE, $baseId);
	}
	$shipQueueManager->changeSession($S_SQM1);

	# check the ship queues of dock 2
	$S_SQM2 = $shipQueueManager->getCurrentSession();
	$shipQueueManager->newSession();
	$shipQueueManager->load(array('rOrbitalBase' => $baseId, 'dockType' => 2), array('dEnd'));
	for ($j = 0; $j < $shipQueueManager->size(); $j++) { 
		$date = $shipQueueManager->get($j)->dEnd;
		$session->get('playerEvent')->add($date, EVENT_BASE, $baseId);
	}
	$shipQueueManager->changeSession($S_SQM2);

	# check the ship queues of dock 3
	$S_SQM3 = $shipQueueManager->getCurrentSession();
	$shipQueueManager->newSession();
	$shipQueueManager->load(array('rOrbitalBase' => $baseId, 'dockType' => 3), array('dEnd'));
	for ($j = 0; $j < $shipQueueManager->size(); $j++) { 
		$date = $shipQueueManager->get($j)->dEnd;
		$session->get('playerEvent')->add($date, EVENT_BASE, $baseId);
	}
	$shipQueueManager->changeSession($S_SQM3);

	# check the technology queues
	$S_TQM1 = $technologyQueueManager->getCurrentSession();
	$technologyQueueManager->newSession();
	$technologyQueueManager->load(array('rPlace' => $baseId), array('dEnd'));
	for ($j = 0; $j < $technologyQueueManager->size(); $j++) { 
		$date = $technologyQueueManager->get($j)->dEnd;
		$session->get('playerEvent')->add($date, EVENT_BASE, $baseId);
	}
	$technologyQueueManager->changeSession($S_TQM1);
}

# check the commanders (outgoing attacks)
$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::MOVING]);

foreach ($commanders as $commander) { 
	if ($commander->getTravelType() === Commander::MOVE) {
		continue;
	}
	$session->get('playerEvent')->add(
		$commander->getArrivalDate(),
		EVENT_OUTGOING_ATTACK,
		$commander->getId(),
		$commanderManager->getEventInfo($commander)
	);
}

# check the incoming attacks
$places = array();
for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
	$places[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}
for ($i = 0; $i < $session->get('playerBase')->get('ms')->size(); $i++) {
	$places[] = $session->get('playerBase')->get('ms')->get($i)->get('id');
}

$incomingCommanders = $commanderManager->getIncomingAttacks($places);

foreach ($incomingCommanders as $commander) { 
	if (in_array($commander->getTypeOfMove(), array(Commander::COLO, Commander::LOOT))) {
		# va chercher les heures auxquelles il rentre dans les cercles d'espionnage
		$startPlace = $placeManager->get($commander->getRBase());
		$destinationPlace = $placeManager->get($commander->getRPlaceDestination());
		$times = Game::getAntiSpyEntryTime($startPlace, $destinationPlace, $commander->getArrivalDate());

		if (strtotime(Utils::now()) >= strtotime($times[0])) {
			$info = $commanderManager->getEventInfo($commander);
			$info->add('inCircle', $times);

			# ajout de l'événement
			$session->get('playerEvent')->add(
				$commanderManager->get($i)->getArrivalDate(), 
				EVENT_INCOMING_ATTACK, 
				$commanderManager->get($i)->getId(),
				$info
			);
		}
	}
}
