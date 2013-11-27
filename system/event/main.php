<?php
// Initialisation des événements
include_once ZEUS;
include_once GAIA;
include_once ARES;

$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(ASM_UMODE);
ASM::$pam->load(array('bind' => self::$get->get('bindkey'), 'statement' => 1));		// self::$get ?!?! pourquoi pas CTR::$get ?

if (ASM::$pam->size() == 1) {
	$player = ASM::$pam->get();

	# création des tableaux de données dans le contrôleur
	CTRHelper::initializePlayerEvent();
	CTRHelper::initializeLastUpdate();

	# remplissage des events
	// check different things for each orbitalBase
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')));
	$now = Utils::now();
	for ($i = 0; $i < ASM::$obm->size(); $i++) { 
		$baseId = ASM::$obm->get($i)->getRPlace();

		// check the building queues
		$S_BQM1 = ASM::$bqm->getCurrentSession();
		ASM::$bqm->newSession();
		ASM::$bqm->load(array('rOrbitalBase' => $baseId), array('position'));
		$remainingTime = 0;
		for ($j = 0; $j < ASM::$bqm->size(); $j++) { 
			$remainingTime += ASM::$bqm->get($j)->getRemainingTime();
			$date = Utils::addSecondsToDate($now, $remainingTime);
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$bqm->changeSession($S_BQM1);

		// check the ship queues of dock 1
		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession();
		ASM::$sqm->load(array('rOrbitalBase' => $baseId, 'dockType' => 1), array('position'));
		$remainingTime = 0;
		for ($j = 0; $j < ASM::$sqm->size(); $j++) { 
			$remainingTime += ASM::$sqm->get($j)->getRemainingTime();
			$date = Utils::addSecondsToDate($now, $remainingTime);
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$sqm->changeSession($S_SQM1);

		// check the ship queues of dock 2
		$S_SQM2 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession();
		ASM::$sqm->load(array('rOrbitalBase' => $baseId, 'dockType' => 2), array('position'));
		$remainingTime = 0;
		for ($j = 0; $j < ASM::$sqm->size(); $j++) { 
			$remainingTime += ASM::$sqm->get($j)->getRemainingTime();
			$date = Utils::addSecondsToDate($now, $remainingTime);
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$sqm->changeSession($S_SQM2);

		// check the ship queues of dock 3
		$S_SQM3 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession();
		ASM::$sqm->load(array('rOrbitalBase' => $baseId, 'dockType' => 3), array('position'));
		$remainingTime = 0;
		for ($j = 0; $j < ASM::$sqm->size(); $j++) { 
			$remainingTime += ASM::$sqm->get($j)->getRemainingTime();
			$date = Utils::addSecondsToDate($now, $remainingTime);
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$sqm->changeSession($S_SQM3);

		// check the technology queues
		$S_TQM1 = ASM::$tqm->getCurrentSession();
		ASM::$tqm->newSession();
		ASM::$tqm->load(array('rPlace' => $baseId), array('position'));
		$remainingTime = 0;
		for ($j = 0; $j < ASM::$tqm->size(); $j++) { 
			$remainingTime += ASM::$tqm->get($j)->remainingTime;
			$date = Utils::addSecondsToDate($now, $remainingTime);
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$tqm->changeSession($S_TQM1);
	}
	ASM::$obm->changeSession($S_OBM1);

	// check the commanders (outgoing attacks)
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('rPlayer' => CTR::$data->get('playerId'), 'statement' => COM_MOVING, 'typeOfMove' => array(COM_LOOT, COM_COLO)));

	for ($i = 0; $i < ASM::$com->size(); $i++) { 
		CTR::$data->get('playerEvent')->add(ASM::$com->get($i)->getArrivalDate(), EVENT_OUTGOING_ATTACK, ASM::$com->get($i)->getId());
	}
	ASM::$com->changeSession($S_COM1);

	// check the incoming attacks
	$S_COM2 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	$places = array();
	for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
		$places[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
	}
	for ($i = 0; $i < CTR::$data->get('playerBase')->get('ms')->size(); $i++) {
		$places[] = CTR::$data->get('playerBase')->get('ms')->get($i)->get('id');
	}
	ASM::$com->load(array('rPlaceDestination' => $places, 'statement' => COM_MOVING, 'typeOfMove' => array(COM_LOOT, COM_COLO)));

	// ajout des bases des ennemis dans le tableau
	for ($i = 0; $i < ASM::$com->size(); $i++) {
		$places[] = ASM::$com->get($i)->getRBase();
	}

	
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession();
	ASM::$plm->load(array('id' => $places));

	for ($i = 0; $i < ASM::$com->size(); $i++) { 
		if (in_array(ASM::$com->get($i)->getTypeOfMove(), array(COM_COLO, COM_LOOT))) {
			// va chercher les heures auxquelles il rentre dans les cercles d'espionnage
			$startPlace = ASM::$plm->getById(ASM::$com->get($i)->getRBase());
			$destinationPlace = ASM::$plm->getById(ASM::$com->get($i)->getRPlaceDestination());

			$times = Game::getAntiSpyEntryTime($startPlace, $destinationPlace, ASM::$com->get($i)->getArrivalDate());

			CTR::$data->get('playerEvent')->add(ASM::$com->get($i)->getArrivalDate(), EVENT_INCOMING_ATTACK, ASM::$com->get($i)->getId(), $times);
		}
	}
	ASM::$plm->changeSession($S_PLM1);
	ASM::$com->changeSession($S_COM2);


	CTR::redirect('profil');
} else { 
	header('Location: ' . GETOUT_ROOT . '?w=noplayerfound');
	exit();
}

ASM::$pam->changeSession($S_PAM1);
?>