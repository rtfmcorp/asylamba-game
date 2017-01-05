<?php
# exécute les événements s'il sont dans la liste

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Container\StackList;

$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

$now = Utils::now();

$pastEvents = $session->get('playerEvent')->getPastEvents($now); # stacklist

if ($pastEvents->size() > 0) {
	$rPlaces = new StackList();	# liste des bases orbitales déjà mises à jour

	for ($i = 0; $i < $pastEvents->size(); $i++) { 
		$event = $pastEvents->get($i);

		# événements concernant les bases orbitales
		if ($event->get('eventType') == EVENT_BASE) {
			if (!$rPlaces->exist($event->get('eventId'))) {
				# ajout de la base dans la liste des bases déjà mises à jour
				$rPlaces->add($event->get('eventId'), $event->get('eventId'));
				# mise à jour de la base orbitale (avec u-méthodes)
				$S_OBM1 = $orbitalBaseManager->getCurrentSession();
				$orbitalBaseManager->newSession(ASM_UMODE);
				$orbitalBaseManager->load(array('rPlace' => $event->get('eventId')));
				$orbitalBaseManager->changeSession($S_OBM1);
			}
		}

		# événements concernant les attaques sortantes et entrantes
		if ($event->get('eventType') == EVENT_OUTGOING_ATTACK OR $event->get('eventType') == EVENT_INCOMING_ATTACK) {
			# mise à jour du commandant qui fait l'attaque 
			$S_COM1 = $commanderManager->getCurrentSession();
			$commanderManager->newSession(ASM_UMODE);
			$commanderManager->load(array('c.id' => $event->get('eventId')));
			$commanderManager->changeSession($S_COM1);
		}

	}
	$session->get('playerEvent')->clearPastEvents($now);
}
