<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;

$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');

# chargement des événements concernant les flottes qui attaquent le joueur
if (Utils::interval($session->get('lastUpdate')->get('event'), Utils::now(), 's') > TIME_EVENT_UPDATE) {

	# update de l'heure dans le contrôleur
	$session->get('lastUpdate')->add('event', Utils::now());

	# ajout des événements des flottes entrantes dans le périmètre de contre-espionnage
	$places = array();
	for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
		$places[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
	}
	for ($i = 0; $i < $session->get('playerBase')->get('ms')->size(); $i++) {
		$places[] = $session->get('playerBase')->get('ms')->get($i)->get('id');
	}

	$commanders = $commanderManager->getIncomingAttacks($places);

	# ajout des bases des ennemis dans le tableau
	foreach ($commanders as $commander) {
		$places[] = $commander->getRBase();
	}
	$placeManager->getByIds($places);

	# enlève du controller tous les évents d'attaques entrantes avant de mettre les nouvelles
	$size = $session->get('playerEvent')->size();
	for ($i = 0; $i < $size; $i++) {
		if ($session->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
			$session->get('playerEvent')->remove($i);
			$i--;
			$size--;
		}
	}
	
	foreach ($commanders as $commander) { 
		# va chercher les heures auxquelles il rentre dans les cercles d'espionnage
		$startPlace = $placeManager->get($commander->getRBase());
		$destinationPlace = $placeManager->get($commander->getRPlaceDestination());
		$times = Game::getAntiSpyEntryTime($startPlace, $destinationPlace, $commander->getArrivalDate());

		if (strtotime(Utils::now()) >= strtotime($times[0])) {
			$info = $commanderManager->getEventInfo($commander);
			$info->add('inCircle', $times);

			# ajout de l'événement
			$session->get('playerEvent')->add(
				$commander->getArrivalDate(), 
				EVENT_INCOMING_ATTACK, 
				$commander->getId(),
				$info
			);
		}
	}
	# mettre à jour dLastActivity
	$player = $playerManager->get($session->get('playerId'));
	$player->setDLastActivity(Utils::now());
	$this->getContainer()->get('entity_manager')->flush($player);
}
