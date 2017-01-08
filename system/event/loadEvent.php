<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Ares\Model\Commander;

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

	$S_COM2 = $commanderManager->getCurrentSession();
	$commanderManager->newSession();
	$commanderManager->load(array('c.rDestinationPlace' => $places, 'c.statement' => Commander::MOVING, 'c.travelType' => array(Commander::LOOT, Commander::COLO)));

	# ajout des bases des ennemis dans le tableau
	for ($i = 0; $i < $commanderManager->size(); $i++) {
		$places[] = $commanderManager->get($i)->getRBase();
	}
	$S_PLM1 = $placeManager->getCurrentSession();
	$placeManager->newSession();
	$placeManager->load(array('id' => $places));

	# enlève du controller tous les évents d'attaques entrantes avant de mettre les nouvelles
	$size = $session->get('playerEvent')->size();
	for ($i = 0; $i < $size; $i++) {
		if ($session->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
			$session->get('playerEvent')->remove($i);
			$i--;
			$size--;
		}
	}
	
	for ($i = 0; $i < $commanderManager->size(); $i++) { 
		# va chercher les heures auxquelles il rentre dans les cercles d'espionnage
		$startPlace = $placeManager->getById($commanderManager->get($i)->getRBase());
		$destinationPlace = $placeManager->getById($commanderManager->get($i)->getRPlaceDestination());
		$times = Game::getAntiSpyEntryTime($startPlace, $destinationPlace, $commanderManager->get($i)->getArrivalDate());

		if (strtotime(Utils::now()) >= strtotime($times[0])) {
			$info = $commanderManager->getEventInfo($commanderManager->get($i));
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
	$placeManager->changeSession($S_PLM1);
	$commanderManager->changeSession($S_COM2);

	# mettre à jour dLastActivity
	$player = $playerManager->get($session->get('playerId'));
	$player->setDLastActivity(Utils::now());
	$this->getContainer()->get('entity_manager')->flush($player);
}
