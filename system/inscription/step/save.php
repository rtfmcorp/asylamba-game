<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Promethee\Model\Research;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Classes\Worker\API;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Gaia\Event\PlaceOwnerChangeEvent;

try {
	$session = $this->getContainer()->get('app.session');
	$playerManager = $this->getContainer()->get('zeus.player_manager');
	$notificationManager = $this->getContainer()->get('hermes.notification_manager');
	$researchManager = $this->getContainer()->get('promethee.research_manager');
	$researchHelper = $this->getContainer()->get('promethee.research_helper');
	$database = $this->getContainer()->get('database');
	$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
	$placeManager = $this->getContainer()->get('gaia.place_manager');
	$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
	$entityManager = $this->getContainer()->get('entity_manager');
	$eventDispatcher = $this->getContainer()->get('event_dispatcher');
	
	$faction = $session->get('inscription')->get('ally');
	# AJOUT DU JOUEUR EN BASE DE DONNEE
	$player = new Player();

	# ajout des variables inchangées
	$player->setBind($session->get('inscription')->get('bindkey'));
	$player->setRColor($session->get('inscription')->get('ally'));
	$player->setName(trim($session->get('inscription')->get('pseudo')));
	$player->setAvatar($session->get('inscription')->get('avatar'));

	$playerManager->saveSessionData($player);
	
	if ($session->exist('rgodfather')) {
		$player->rGodfather = $session->get('rgodfather');
	}

	$player->setStatus(1);
	$player->uPlayer = Utils::now();

	$player->victory = 0;
	$player->defeat = 0;

	$player->stepTutorial = 1;
	$player->stepDone = TRUE;

	$player->iUniversity = 1000;
	$player->partNaturalSciences = 25;
	$player->partLifeSciences = 25;
	$player->partSocialPoliticalSciences = 25;
	$player->partInformaticEngineering = 25;
	
	$player->setDInscription(Utils::now());
	$player->setDLastConnection(Utils::now());
	$player->setDLastActivity(Utils::now());

	$player->setPremium(0);
	$player->setStatement(1);

	# ajout des variables dépendantes
	if ($session->get('high-mode')) {
		$player->credit = 10000000;
		$player->setExperience(18000);
		$player->setLevel(5);
	} else {
		$player->credit = 5000;
		$player->setExperience(630);
		$player->setLevel(1);
	}
	
	$playerManager->add($player);

	if ($session->exist('rgodfather')) {
		# send a message to the godfather
		$n = new Notification();
		$n->setRPlayer($player->rGodfather);
		$n->setTitle('Votre filleul s\'est inscrit');
		$n->addBeg()->addTxt('Un de vos amis a créé un compte.')->addSep();
		$n->addTxt('Vous pouvez le contacter, son nom de joueur est ');
		$n->addLnk('embassy/player-' . $player->getId(), '"' . $player->name . '"')->addTxt('.');
		$n->addBrk()->addTxt('Vous venez de gagner 1000 crédits. Vous en gagnerez 1 million de plus lorsqu\'il atteindra le niveau 3.');
		$n->addEnd();

		$notificationManager->add($n);

		# add 1000 credits to the godfather
		if (($godFather = $playerManager->get($player->rGodFather))) {
			$playerManager->increaseCredit($godFather, 1000);
		}

		# remove godFather from session
		$session->remove('rgodfather');
	}

	# INITIALISATION DES RECHERCHES
		# rendre aléatoire
	$rs = new Research();
	$rs->rPlayer = $player->getId();

	if ($session->get('high-mode')) {
		$rs->mathLevel = 15;
		$rs->physLevel = 15;
		$rs->chemLevel = 15;
		$rs->bioLevel = 15;
		$rs->mediLevel = 15;
		$rs->econoLevel = 15;
		$rs->psychoLevel = 15;
		$rs->networkLevel = 15;
		$rs->algoLevel = 15;
		$rs->statLevel = 15;
	}

	$rs->naturalTech = Research::MATH;
	$rs->lifeTech = Research::LAW;
	$rs->socialTech = Research::ECONO;
	$rs->informaticTech = Research::NETWORK;

	$rs->naturalToPay = $researchHelper->getInfo($rs->naturalTech, 'level', 1, 'price');
	$rs->lifeToPay = $researchHelper->getInfo($rs->lifeTech, 'level', 1, 'price');
	$rs->socialToPay = $researchHelper->getInfo($rs->socialTech, 'level', 1, 'price');
	$rs->informaticToPay = $researchHelper->getInfo($rs->informaticTech, 'level', 1, 'price');
	$researchManager->add($rs);

	# CREATION DE LA BASE ORBITALE
	$ob = new OrbitalBase();

	# choix de la place
	$qr = $database->prepare('SELECT * FROM place AS p
		INNER JOIN system AS sy ON p.rSystem = sy.id
			INNER JOIN sector AS se ON sy.rSector = se.id
		WHERE p.typeOfPlace = 1
			AND se.id = ?
			AND p.rPlayer IS NULL
		ORDER BY p.population ASC
		LIMIT 0, 30'
	);
	$qr->execute(array($session->get('inscription')->get('sector')));
	$aw = $qr->fetchAll();

	$placeId = $aw[rand(0, (count($aw) - 1))][0];

	$ob->setRPlace($placeId);

	$ob->setRPlayer($player->getId());
	$ob->setName($session->get('inscription')->get('base'));
	
	# création des premiers bâtiments
	if ($session->get('high-mode')) {
		# batiments haut-level
		$ob->setLevelGenerator(35);
		$ob->setLevelRefinery(35);
		$ob->setLevelDock1(35);
		$ob->setLevelDock2(10);
		$ob->setLevelDock3(0);
		$ob->setLevelTechnosphere(35);
		$ob->setLevelCommercialPlateforme(10);
		$ob->setLevelStorage(35);
		$ob->setLevelRecycling(15);
		$ob->setLevelSpatioport(10);
		$ob->setResourcesStorage(3000000);

		# remplir le dock
		$orbitalBaseManager->addShipToDock($ob, 1, 50);
		$orbitalBaseManager->addShipToDock($ob, 2, 50);
		$orbitalBaseManager->addShipToDock($ob, 3, 10);
		$orbitalBaseManager->addShipToDock($ob, 4, 10);
		$orbitalBaseManager->addShipToDock($ob, 5, 5);
		$orbitalBaseManager->addShipToDock($ob, 6, 5);
		$orbitalBaseManager->addShipToDock($ob, 7, 2);
		$orbitalBaseManager->addShipToDock($ob, 8, 2);
		$orbitalBaseManager->addShipToDock($ob, 9, 1);
		$orbitalBaseManager->addShipToDock($ob, 10, 1);
		$orbitalBaseManager->addShipToDock($ob, 11, 0);
		$orbitalBaseManager->addShipToDock($ob, 12, 0);
	} else {
		$ob->setLevelGenerator(1);
		$ob->setLevelRefinery(1);
		$ob->setLevelDock1(0);
		$ob->setLevelDock2(0);
		$ob->setLevelDock3(0);
		$ob->setLevelTechnosphere(0);
		$ob->setLevelCommercialPlateforme(0);
		$ob->setLevelStorage(1);
		$ob->setLevelRecycling(0);
		$ob->setLevelSpatioport(0);
		$ob->setResourcesStorage(1000);
	}
	
	$orbitalBaseManager->updatePoints($ob);

	# initialisation des investissement
	$ob->setISchool(500);
	$ob->setIAntiSpy(500);

	# ajout de la base
	$ob->uOrbitalBase = Utils::now();
	$ob->setDCreation(Utils::now());
	$orbitalBaseManager->add($ob);

	# ajout des techs haut-level
	if ($session->get('high-mode')) {
		$technologyManager = $this->getContainer()->get('promethee.technology_manager');
		$technologyManager->addTech($player->id, Technology::COM_PLAT_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::DOCK2_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::DOCK3_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::RECYCLING_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SPATIOPORT_UNBLOCK, 1);

		$technologyManager->addTech($player->id, Technology::SHIP0_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP1_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP2_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP3_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP4_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP5_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP6_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP7_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP8_UNBLOCK, 1);
		$technologyManager->addTech($player->id, Technology::SHIP9_UNBLOCK, 1);

		$technologyManager->addTech($player->id, Technology::COLONIZATION, 1);
		$technologyManager->addTech($player->id, Technology::CONQUEST, 1);
		$technologyManager->addTech($player->id, Technology::BASE_QUANTITY, 4);
	}

	# modification de la place
	$place = $placeManager->get($placeId);
	$place->setRPlayer($player->getId());
	$place->population = 50;
	$place->coefResources = 60;
	$place->coefHistory = 20;
	$entityManager->flush($place);
	
	$eventDispatcher->dispatch(new PlaceOwnerChangeEvent($place));

	# confirmation au portail
	if ($this->getContainer()->getParameter('apimode') === 'enabled') {
		$return = $this->getContainer()->get('api')->confirmInscription($session->get('inscription')->get('bindkey'));
	}

	# enregistrement DA
	if (DATA_ANALYSIS) {
		$qr = $database->prepare('INSERT INTO 
			DA_Player(id, color, dInscription)
			VALUES(?, ?, ?)'
		);
		$qr->execute([$player->getId(), $player->rColor, Utils::now()]);
	}

	# clear les sessions
	$session->remove('inscription');
	$session->remove('prebindkey');

	# ajout aux conversation de faction et techniques
	$readingDate = date('Y-m-d H:i:s', (strtotime(Utils::now()) - 20));
	
	if (($factionAccount = $playerManager->getFactionAccount($player->rColor)) !== null) {
		$S_CVM = $conversationManager->getCurrentSession();
		$conversationManager->newSession();
		$conversationManager->load([
				'cu.rPlayer' => [ID_JEANMI, $factionAccount->id]
			], [], [0, 2]
		);

		for ($i = 0; $i < $conversationManager->size(); $i++) { 
			$user = new ConversationUser();
			$user->rConversation = $conversationManager->get($i)->id;
			$user->rPlayer = $player->getId();
			$user->convPlayerStatement = ConversationUser::US_STANDARD;
			$user->convStatement = ConversationUser::CS_ARCHIVED;
			$user->dLastView = $readingDate;

			$this->getContainer()->get('hermes.conversation_user_manager')->add($user);
		}
		
		$conversationManager->changeSession($S_CVM);
	}
	$security = $this->getContainer()->get('security');
	# redirection vers connection
	$this->getContainer()->get('app.response')->redirect('connection/bindkey-' . $security->crypt($security->buildBindkey($player->getBind())) . '/mode-splash');
} catch (Exception $e) {
	# tentative de réparation de l'erreur
	$this->getContainer()->get('app.response')->redirect('inscription/step-3');
	// Transmit the exception to the error handler
	throw $e;
}
