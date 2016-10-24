<?php
# exécute les événements s'il sont dans la liste

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;

$now = Utils::now();

$pastEvents = CTR::$data->get('playerEvent')->getPastEvents($now); # stacklist

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
				$S_OBM1 = ASM::$obm->getCurrentSession();
				ASM::$obm->newSession(ASM_UMODE);
				ASM::$obm->load(array('rPlace' => $event->get('eventId')));
				ASM::$obm->changeSession($S_OBM1);
			}
		}

		# événements concernant les attaques sortantes et entrantes
		if ($event->get('eventType') == EVENT_OUTGOING_ATTACK OR $event->get('eventType') == EVENT_INCOMING_ATTACK) {
			# mise à jour du commandant qui fait l'attaque 
			$S_COM1 = ASM::$com->getCurrentSession();
			ASM::$com->newSession(ASM_UMODE);
			ASM::$com->load(array('c.id' => $event->get('eventId')));
			ASM::$com->changeSession($S_COM1);
		}

	}
	CTR::$data->get('playerEvent')->clearPastEvents($now);
}
