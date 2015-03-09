<?php
include_once GAIA;
include_once ATHENA;
include_once ZEUS;
include_once PROMETHEE;
include_once ARES;
include_once HERMES;

try {
	$faction = CTR::$data->get('inscription')->get('ally');
	# AJOUT DU JOUEUR EN BASE DE DONNEE
	$pl = new Player();

	# ajout des variables inchangées
	$pl->setBind(CTR::$data->get('inscription')->get('bindkey'));
	$pl->setRColor(CTR::$data->get('inscription')->get('ally'));
	$pl->setName(trim(CTR::$data->get('inscription')->get('pseudo')));
	$pl->setAvatar(CTR::$data->get('inscription')->get('avatar'));

	$pl->setStatus(1);
	$pl->uPlayer = Utils::now();

	$pl->victory = 0;
	$pl->defeat = 0;

	$pl->stepTutorial = 1;
	$pl->stepDone = TRUE;

	$pl->iUniversity = 1000;
	$pl->partNaturalSciences = 25;
	$pl->partLifeSciences = 25;
	$pl->partSocialPoliticalSciences = 25;
	$pl->partInformaticEngineering = 25;
	
	$pl->setDInscription(Utils::now());
	$pl->setDLastConnection(Utils::now());
	$pl->setDLastActivity(Utils::now());

	$pl->setPremium(0);
	$pl->setStatement(1);

	# ajout des variables dépendantes
	if (CTR::$data->get('high-mode')) {
		$pl->credit = 100000000;
		$pl->setExperience(18000);
		$pl->setLevel(5);
	} else {
		$pl->credit = 5000;
		$pl->setExperience(630);
		$pl->setLevel(1);
	}

	ASM::$pam->add($pl);

	# INITIALISATION DES RECHERCHES
		# rendre aléatoire
	$rs = new Research();
	$rs->rPlayer = $pl->getId();

	if (CTR::$data->get('high-mode')) {
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
	$rs->lifeTech = Research::BIO;
	$rs->socialTech = Research::ECONO;
	$rs->informaticTech = Research::NETWORK;

	$rs->naturalToPay = ResearchResource::getInfo($rs->naturalTech, 'level', 1, 'price');
	$rs->lifeToPay = ResearchResource::getInfo($rs->lifeTech, 'level', 1, 'price');
	$rs->socialToPay = ResearchResource::getInfo($rs->socialTech, 'level', 1, 'price');
	$rs->informaticToPay = ResearchResource::getInfo($rs->informaticTech, 'level', 1, 'price');
	ASM::$rsm->add($rs);

	# CREATION DE LA BASE ORBITALE
	$ob = new OrbitalBase();

	# choix de la place
	$db = Database::getInstance();
	$qr = $db->prepare('SELECT * FROM place AS p
		INNER JOIN system AS sy ON p.rSystem = sy.id
			INNER JOIN sector AS se ON sy.rSector = se.id
		WHERE p.typeOfPlace = 1
			AND se.id = ?
			AND p.rPlayer IS NULL
		ORDER BY p.population ASC
		LIMIT 0, 30'
	);
	$qr->execute(array(CTR::$data->get('inscription')->get('sector')));
	$aw = $qr->fetchAll();

	$place = $aw[rand(0, (count($aw) - 1))][0];

	$ob->setRPlace($place);

	$ob->setRPlayer($pl->getId());
	$ob->setName(CTR::$data->get('inscription')->get('base'));

	# création des premiers bâtiments
	if (CTR::$data->get('high-mode')) {
		# batiments haut-level
		$ob->setLevelGenerator(40);
		$ob->setLevelRefinery(40);
		$ob->setLevelDock1(40);
		$ob->setLevelDock2(12);
		$ob->setLevelDock3(0);
		$ob->setLevelTechnosphere(40);
		$ob->setLevelCommercialPlateforme(12);
		$ob->setLevelStorage(40);
		$ob->setLevelRecycling(20);
		$ob->setLevelSpatioport(12);
		$ob->setResourcesStorage(4000000);

		# remplir le dock
		$ob->addShipToDock(1, 50);
		$ob->addShipToDock(2, 50);
		$ob->addShipToDock(3, 10);
		$ob->addShipToDock(4, 10);
		$ob->addShipToDock(5, 5);
		$ob->addShipToDock(6, 5);
		$ob->addShipToDock(7, 2);
		$ob->addShipToDock(8, 2);
		$ob->addShipToDock(9, 1);
		$ob->addShipToDock(10, 1);
		$ob->addShipToDock(11, 0);
		$ob->addShipToDock(12, 0);
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
	
	$ob->updatePoints();

	# initialisation des investissement
	$ob->setISchool(500);
	$ob->setIAntiSpy(500);

	# ajout de la base
	$ob->uOrbitalBase = Utils::now();
	$ob->setDCreation(Utils::now());
	ASM::$obm->add($ob);

	# ajout des techs haut-level
	if (CTR::$data->get('high-mode')) {
		Technology::addTech($pl->id, Technology::COM_PLAT_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::DOCK2_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::DOCK3_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::RECYCLING_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SPATIOPORT_UNBLOCK, 1);

		Technology::addTech($pl->id, Technology::SHIP0_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP1_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP2_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP3_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP4_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP5_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP6_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP7_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP8_UNBLOCK, 1);
		Technology::addTech($pl->id, Technology::SHIP9_UNBLOCK, 1);

		Technology::addTech($pl->id, Technology::COLONIZATION, 1);
		Technology::addTech($pl->id, Technology::CONQUEST, 1);
		Technology::addTech($pl->id, Technology::BASE_QUANTITY, 4);
	}

	# modification de la place
	ASM::$plm->load(array('id' => $place));
	ASM::$plm->get()->setRPlayer($pl->getId());
	ASM::$plm->get()->population = 50;
	ASM::$plm->get()->coefResources = 60;
	ASM::$plm->get()->coefHistory = 20;

	# confirmation au portail
	if (APIMODE) {
		$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
		$api->confirmInscription(CTR::$data->get('inscription')->get('bindkey'), APP_ID);
	}

	# clear les sessions
	CTR::$data->remove('inscription');
	CTR::$data->remove('prebindkey');

	include_once GAIA;
	GalaxyColorManager::apply();

	# send welcome message with Jean-Mi
	$message = 'Salut,  
		<br /><br />Moi c\'est Jean-Mi, l\'opérateur du jeu. 
		<br />Je te souhaite la bienvenue sur Asylamba et espère que tu t\'y plairas.
		<br />Je t\'enverrai des messages quand tu devras être au courant de choses importantes au fur et à mesure du temps.
		<br /><br />Bon jeu et à bientôt j\'espère.
		<br /><br />Cordialement, <br />Jean-Mi';
	$m = new Message();
	$m->setRPlayerWriter(ID_JEANMI);
	$m->setDSending(Utils::now());
	$m->setContent($message);

	$db = DataBase::getInstance();
	$qr = $db->prepare('SELECT MAX(thread) AS maxThread FROM message');
	$qr->execute();
	
	if ($aw = $qr->fetch()) {
		$m->setThread($aw['maxThread'] + 1);
		$m->setRPlayerReader($pl->getId());
		ASM::$msm->add($m);
	} else {
		CTR::$alert->add('Création du message d\'accueil raté :-(. Bienvenue quand même !', ALERT_STD_ERROR);
	}

	# redirection vers connection
	CTR::redirect('connection/bindkey-' . Security::crypt(Security::buildBindkey($pl->getBind()), KEY_SERVER) . '/mode-splash');
} catch (Exception $e) {
	# tentative de réparation de l'erreur

	CTR::$alert->add('erreur' . $e->getMessage());
	CTR::redirect('inscription/step-3');
}
?>