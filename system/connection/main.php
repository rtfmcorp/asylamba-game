<?php
# vérification du joueur
# ajout des informations dans le managers
include_once ZEUS;
include_once ATHENA;
include_once ARES;
include_once GAIA;

$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(ASM_UMODE);
ASM::$pam->load(array('bind' => CTR::$get->get('bindkey'), 'statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

if (ASM::$pam->size() == 1) {
	$player = ASM::$pam->get();
	$player->setStatement(PAM_ACTIVE);

	# création des tableaux de données dans le contrôler
	CTRHelper::initializePlayerInfo();
	CTRHelper::initializePlayerBase();
	CTRHelper::initializePlayerBonus();

	# remplissage des données du joueur
	CTR::$data->add('playerId', $player->getId());

	CTR::$data->get('playerInfo')->add('color', $player->getRColor());
	CTR::$data->get('playerInfo')->add('name', $player->getName());
	CTR::$data->get('playerInfo')->add('avatar', $player->getAvatar());
	CTR::$data->get('playerInfo')->add('credit', $player->getCredit());
	CTR::$data->get('playerInfo')->add('experience', $player->getExperience());
	CTR::$data->get('playerInfo')->add('level', $player->getLevel());
	CTR::$data->get('playerInfo')->add('stepTutorial', $player->stepTutorial);
	CTR::$data->get('playerInfo')->add('stepDone', $player->stepDone);	

	if (Utils::isAdmin($player->getBind())) {
		CTR::$data->get('playerInfo')->add('admin', TRUE);
	} else {
		CTR::$data->get('playerInfo')->add('admin', FALSE);
	}

	# remplissage des bases
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlayer' => $player->getId()), array('dCreation', 'ASC'));
	for ($i = 0; $i < ASM::$obm->size(); $i++) {
		$base = ASM::$obm->get($i);
		CTRHelper::addBase(
			'ob', $base->getId(), 
			$base->getName(), 
			$base->getSector(), 
			$base->getSystem(), 
			'1-' . Game::getSizeOfPlanet($base->getPlanetPopulation()),
			$base->typeOfBase
		);
	}
	ASM::$obm->changeSession($S_OBM1);

	# remplissage des bonus
	$bonus = new PlayerBonus($player->getId());
	$bonus->initialize();

	# création des paramètres utilisateur
	CTR::$data->add('playerParams', new ArrayList());

	# remplissage des paramètres utilisateur
	CTR::$data->get('playerParams')->add('base', CTR::$data->get('playerBase')->get('ob')->get(0)->get('id'));

	# création des tableaux de données dans le contrôleur
	CTRHelper::initializePlayerEvent();
	CTRHelper::initializeLastUpdate();

	# remplissage des events
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')));
	$now = Utils::now();
	for ($i = 0; $i < ASM::$obm->size(); $i++) { 
		$baseId = ASM::$obm->get($i)->getRPlace();

		# check the building queues
		$S_BQM1 = ASM::$bqm->getCurrentSession();
		ASM::$bqm->newSession();
		ASM::$bqm->load(array('rOrbitalBase' => $baseId), array('dEnd'));
		for ($j = 0; $j < ASM::$bqm->size(); $j++) { 
			$date = ASM::$bqm->get($j)->dEnd;
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$bqm->changeSession($S_BQM1);

		# check the ship queues of dock 1
		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession();
		ASM::$sqm->load(array('rOrbitalBase' => $baseId, 'dockType' => 1), array('dEnd'));
		for ($j = 0; $j < ASM::$sqm->size(); $j++) { 
			$date = ASM::$sqm->get($j)->dEnd;
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$sqm->changeSession($S_SQM1);

		# check the ship queues of dock 2
		$S_SQM2 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession();
		ASM::$sqm->load(array('rOrbitalBase' => $baseId, 'dockType' => 2), array('dEnd'));
		for ($j = 0; $j < ASM::$sqm->size(); $j++) { 
			$date = ASM::$sqm->get($j)->dEnd;
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$sqm->changeSession($S_SQM2);

		# check the ship queues of dock 3
		$S_SQM3 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession();
		ASM::$sqm->load(array('rOrbitalBase' => $baseId, 'dockType' => 3), array('dEnd'));
		for ($j = 0; $j < ASM::$sqm->size(); $j++) { 
			$date = ASM::$sqm->get($j)->dEnd;
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$sqm->changeSession($S_SQM3);

		# check the technology queues
		$S_TQM1 = ASM::$tqm->getCurrentSession();
		ASM::$tqm->newSession();
		ASM::$tqm->load(array('rPlace' => $baseId), array('dEnd'));
		for ($j = 0; $j < ASM::$tqm->size(); $j++) { 
			$date = ASM::$tqm->get($j)->dEnd;
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);
		}
		ASM::$tqm->changeSession($S_TQM1);
	}
	ASM::$obm->changeSession($S_OBM1);

	# check the commanders (outgoing attacks)
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('c.rPlayer' => CTR::$data->get('playerId'), 'c.statement' => COM_MOVING, 'c.travelType' => array(COM_LOOT, COM_COLO)));

	for ($i = 0; $i < ASM::$com->size(); $i++) { 
		CTR::$data->get('playerEvent')->add(ASM::$com->get($i)->getArrivalDate(), EVENT_OUTGOING_ATTACK, ASM::$com->get($i)->getId());
	}
	ASM::$com->changeSession($S_COM1);

	# check the incoming attacks
	$S_COM2 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	$places = array();
	for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
		$places[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
	}
	for ($i = 0; $i < CTR::$data->get('playerBase')->get('ms')->size(); $i++) {
		$places[] = CTR::$data->get('playerBase')->get('ms')->get($i)->get('id');
	}
	ASM::$com->load(array('c.rDestinationPlace' => $places, 'c.statement' => COM_MOVING, 'c.TravelType' => array(COM_LOOT, COM_COLO)));

	# ajout des bases des ennemis dans le tableau
	for ($i = 0; $i < ASM::$com->size(); $i++) {
		$places[] = ASM::$com->get($i)->getRBase();
	}
	
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession();
	ASM::$plm->load(array('id' => $places));

	for ($i = 0; $i < ASM::$com->size(); $i++) { 
		if (in_array(ASM::$com->get($i)->getTypeOfMove(), array(COM_COLO, COM_LOOT))) {
			# va chercher les heures auxquelles il rentre dans les cercles d'espionnage
			$startPlace = ASM::$plm->getById(ASM::$com->get($i)->getRBase());
			$destinationPlace = ASM::$plm->getById(ASM::$com->get($i)->getRPlaceDestination());

			$times = Game::getAntiSpyEntryTime($startPlace, $destinationPlace, ASM::$com->get($i)->getArrivalDate());

			CTR::$data->get('playerEvent')->add(ASM::$com->get($i)->getArrivalDate(), EVENT_INCOMING_ATTACK, ASM::$com->get($i)->getId(), $times);
		}
	}
	ASM::$plm->changeSession($S_PLM1);
	ASM::$com->changeSession($S_COM2);

	# mise de dLastConnection + dLastActivity
	$player->setDLastConnection(Utils::now());
	$player->setDLastActivity(Utils::now());

	# confirmation au portail
	$api = new API(GETOUT_ROOT);
	$api->confirmConnection(CTR::$get->get('bindkey'), APP_ID);

	# redirection vers page de départ
	CTR::redirect('profil');
} else { 
	header('Location: ' . GETOUT_ROOT . 'accueil/speak-noplayerfound');
	exit();
}

ASM::$pam->changeSession($S_PAM1);
?>