<?php
// chargement des événements concernant les flottes qui attaquent le joueur
include_once ZEUS;
include_once ARES;
include_once GAIA;

if (Utils::interval(CTR::$data->get('lastUpdate')->get('event'), Utils::now(), 's') > TIME_EVENT_UPDATE) {
	// update de l'heure dans le contrôleur
	CTR::$data->get('lastUpdate')->add('event', Utils::now());

	// ajout des événements des flottes entrantes dans le périmètre de contre-espionnage
	$S_COM2 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	$places = array();
	for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
		$places[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
	}
	for ($i = 0; $i < CTR::$data->get('playerBase')->get('ms')->size(); $i++) {
		$places[] = CTR::$data->get('playerBase')->get('ms')->get($i)->get('id');
	}
	ASM::$com->load(array('c.rDestinationPlace' => $places, 'c.statement' => COM_MOVING, 'c.travelType' => array(COM_LOOT, COM_COLO)));

	// ajout des bases des ennemis dans le tableau
	for ($i = 0; $i < ASM::$com->size(); $i++) {
		$places[] = ASM::$com->get($i)->getRBase();
	}
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession();
	ASM::$plm->load(array('id' => $places));

	// enlève du controller tous les évents d'attaques entrantes avant de mettre les nouvelles
	$size = CTR::$data->get('playerEvent')->size();
	for ($i = 0; $i < $size; $i++) {
		if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
			CTR::$data->get('playerEvent')->remove($i);
			$i--;
			$size--;
		}
	}
	
	for ($i = 0; $i < ASM::$com->size(); $i++) { 
		// va chercher les heures auxquelles il rentre dans les cercles d'espionnage
		$startPlace = ASM::$plm->getById(ASM::$com->get($i)->getRBase());
		$destinationPlace = ASM::$plm->getById(ASM::$com->get($i)->getRPlaceDestination());
		$times = Game::getAntiSpyEntryTime($startPlace, $destinationPlace, ASM::$com->get($i)->getArrivalDate());
		// ajout de l'événement
		CTR::$data->get('playerEvent')->add(ASM::$com->get($i)->getArrivalDate(), EVENT_INCOMING_ATTACK, ASM::$com->get($i)->getId(), $times);
	}
	ASM::$plm->changeSession($S_PLM1);
	ASM::$com->changeSession($S_COM2);

	// mettre à jour dLastActivity
	$db = Database::getInstance();
	$qr = $db->prepare('UPDATE player SET dLastActivity = NOW() WHERE id = ?');
	$qr->execute(array(CTR::$data->get('playerId')));
}
?>