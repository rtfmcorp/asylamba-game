<?php
include_once GAIA;
include_once ATHENA;
include_once ZEUS;
include_once PROMETHEE;

try {
	# AJOUT DU JOUEUR EN BASE DE DONNEE
	$pl = new Player();
	$pl->setBind(CTR::$data->get('inscription')->get('bindkey'));
	$pl->setRColor(CTR::$data->get('inscription')->get('ally'));
	$pl->setName(CTR::$data->get('inscription')->get('pseudo'));
	$pl->setAvatar(CTR::$data->get('inscription')->get('avatar'));
	$pl->setStatus(1);
	
	# modifier si negore
	$pl->setCredit(12500);
	$pl->setUCredit(Utils::now());

	$pl->setActionPoint(10);
	$pl->setUActionPoint(Utils::now());

	# modifier l'expérience de base
	$pl->setExperience(630);
	$pl->setLevel(1);

	$pl->setDInscription(Utils::now());
	$pl->setDLastConnection(Utils::now());
	$pl->setDLastActivity(Utils::now());

	$pl->setPremium(1);
	$pl->setStatement(1);

	ASM::$pam->add($pl);

	# INITIALISATION DES RECHERCHES
		# rendre aléatoire
		# modifier les bonus
	$rs = new Research();
	$rs->rPlayer = $pl->getId();
	$rs->naturalTech = 0;
	$rs->lifeTech = 3;
	$rs->socialTech = 5;
	$rs->informaticTech = 7;
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
	$ob->setLevelGenerator(1);
	$ob->setLevelRefinery(1);
	$ob->setLevelDock1(1);
	$ob->setLevelDock2(0);
	$ob->setLevelDock3(0);
	$ob->setLevelTechnosphere(1);
	$ob->setLevelCommercialPlateforme(0);
	$ob->setLevelGravitationalModule(0);
	$ob->updatePoints();

	# initialisation des investissement
		# + ajout des bonus de factions
	$ob->setISchool(500);
	$ob->setIAntiSpy(500);
	$ob->setIUniversity(4000);

	$ob->setPartNaturalSciences(30);
	$ob->setPartInformaticEngineering(30);
	$ob->setPartLifeSciences(20);
	$ob->setPartSocialPoliticalSciences(20);

	# ajout de vaisseau en fonction de la faction
	#$ob->setShipStorage();

	# initialisation des ressources
		# + ajout des bonus de factions
	$ob->setResourcesStorage(3000);

	$ob->setUResources(Utils::now());
	$ob->setUBuildingQueue(Utils::now());
	$ob->setUShipQueue1(Utils::now());
	$ob->setUShipQueue2(Utils::now());
	$ob->setUShipQueue3(Utils::now());
	$ob->setUTechnoQueue(Utils::now());
	$ob->setDCreation(Utils::now());
	ASM::$obm->add($ob);

	# modification de la place
	ASM::$plm->load(array('id' => $place));
	ASM::$plm->get(0)->setRPlayer($pl->getId());

	# confirmation au portail
	$api = new API(GETOUT_ROOT);
	$api->confirmInscription(CTR::$data->get('inscription')->get('bindkey'), APP_ID);

	# clear les sessions
	CTR::$data->remove('inscription');
	CTR::$data->remove('prebindkey');

	# redirection vers connection
	CTR::redirect('connection/bindkey-' . $pl->getBind());
} catch (Exception $e) {
	# tentative de réparation de l'erreur

	CTR::$alert->add('erreur');
	CTR::redirect('inscription/step-3');
}
?>