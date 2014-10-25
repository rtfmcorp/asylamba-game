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

	$pl->setBind(CTR::$data->get('inscription')->get('bindkey'));
	$pl->setRColor(CTR::$data->get('inscription')->get('ally'));
	$pl->setName(trim(CTR::$data->get('inscription')->get('pseudo')));
	$pl->setAvatar(CTR::$data->get('inscription')->get('avatar'));
	$pl->setStatus(1);
	
	if ($faction == 3) {
		# Négore, 12500 crédits de plus
		$pl->credit = 17500;
	} else {
		$pl->credit = 5000;
	}
	$pl->uPlayer = Utils::now();

	# modifier l'expérience de base
	$pl->setExperience(630);
	$pl->setLevel(1);

	$pl->victory = 0;
	$pl->defeat = 0;
	
	$pl->stepTutorial = 1;
	$pl->stepDone = FALSE;

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

	ASM::$pam->add($pl);

	# INITIALISATION DES RECHERCHES
		# rendre aléatoire
	$rs = new Research();
	$rs->rPlayer = $pl->getId();

	$rs->naturalTech = 0;
	$rs->lifeTech = 3;
	$rs->socialTech = 5;
	$rs->informaticTech = 7;

	if ($faction == 6) {
		# Aphéra, 3 recherches niveau 2
		$rs->naturalToPay = ResearchResource::getInfo($rs->naturalTech, 'level', 2, 'price');
		$rs->lifeToPay = ResearchResource::getInfo($rs->lifeTech, 'level', 2, 'price');
		$rs->socialToPay = ResearchResource::getInfo($rs->socialTech, 'level', 2, 'price');
		$rs->informaticToPay = ResearchResource::getInfo($rs->informaticTech, 'level', 1, 'price');
	} else {
		$rs->naturalToPay = ResearchResource::getInfo($rs->naturalTech, 'level', 1, 'price');
		$rs->lifeToPay = ResearchResource::getInfo($rs->lifeTech, 'level', 1, 'price');
		$rs->socialToPay = ResearchResource::getInfo($rs->socialTech, 'level', 1, 'price');
		$rs->informaticToPay = ResearchResource::getInfo($rs->informaticTech, 'level', 1, 'price');
	}
	ASM::$rsm->add($rs);

	# CREATION DE LA BASE ORBITALE
	$ob = new OrbitalBase();

	# choix de la place
	$db = Database::getInstance();
	$qr = $db->prepare('SELECT * FROM place AS p
		INNER JOIN system AS sy ON p.rSystem = sy.id
			INNER JOIN sector AS se ON sy.rSector = se.id
		WHERE p.population > 30 AND se.id = ? AND p.rPlayer = 0
		ORDER BY p.population ASC LIMIT 0, 30');
	$qr->execute(array(CTR::$data->get('inscription')->get('sector')));
	$aw = $qr->fetchAll();
	$place = $aw[rand(0, (count($aw) - 1))][0];
	$ob->setRPlace($place);

	$ob->setRPlayer($pl->getId());
	$ob->setName(CTR::$data->get('inscription')->get('base'));

	# création des premiers bâtiments
		# + ajout des bonus de factions
	if ($faction == 1) {
		# Empire, générateur niveau 5
		$ob->setLevelGenerator(5);
		$pl->stepDone = TRUE;
	} else {
		$ob->setLevelGenerator(1);
	}
	if ($faction == 5) {
		# Nerve, raffinerie niveau 5
		$ob->setLevelRefinery(5);
	} else {
		$ob->setLevelRefinery(1);	
	}
	$ob->setLevelDock1(0);
	$ob->setLevelDock2(0);
	$ob->setLevelDock3(0);
	$ob->setLevelTechnosphere(0);
	$ob->setLevelCommercialPlateforme(0);
	$ob->setLevelStorage(1);
	$ob->setLevelRecycling(0);
	$ob->setLevelSpatioport(0);
	$ob->updatePoints();

	# initialisation des investissement
	$ob->setISchool(500);
	$ob->setIAntiSpy(500);

	# ajout de vaisseau en fonction de la faction
	if ($faction == 2) {
		# Kovakh, 3 Méduses
		$ob->setShipStorage(4, 3);
	}

	# initialisation des ressources
	$ob->setResourcesStorage(1000);


	$ob->uOrbitalBase = Utils::now();
	$ob->setDCreation(Utils::now());
	ASM::$obm->add($ob);

	# ajout d'un commandant
	if ($faction == 4 || $faction == 7) {
		$newCommander = new Commander();
		if ($faction == 4) {
			# Cardan, un commandant niveau 6
			$newCommander->upExperience(rand(3000, 5000));
		}
		if ($faction == 7) {
			# Synelle, un commandant niveau 7
			$newCommander->upExperience(rand(6000, 9000));
		}
		$newCommander->rPlayer = $pl->getId();
		$newCommander->rBase = $ob->getId();
		$newCommander->palmares = 0;
		$newCommander->statement = 0;
		$newCommander->name = CheckName::randomize();
		$newCommander->avatar = 't' . rand(1, 21) . '-c' . $faction;
		$newCommander->dCreation = Utils::now();
		$newCommander->uCommander = Utils::now();
		$newCommander->setSexe(1);
		$newCommander->setAge(rand(40, 70));

		ASM::$com->add($newCommander);
	}

	# modification de la place
	ASM::$plm->load(array('id' => $place));
	ASM::$plm->get(0)->setRPlayer($pl->getId());
	// ressource : 45% ou 46% ou 47%
	ASM::$plm->get(0)->coefResources = rand(45, 47);
	// population : entre 35M et 38M
	ASM::$plm->get(0)->population = rand(3500, 3800) / 100;

	# confirmation au portail
	if (PORTALMODE) {
		$api = new API(GETOUT_ROOT);
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
	$m->setRPlayerWriter(0);
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
	CTR::redirect('connection/bindkey-' . $pl->getBind() . '/mode-splash');
} catch (Exception $e) {
	# tentative de réparation de l'erreur

	CTR::$alert->add('erreur');
	CTR::redirect('inscription/step-3');
}
?>