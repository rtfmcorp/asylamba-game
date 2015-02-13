<?php
include_once ATHENA;
include_once ZEUS;
include_once GAIA;
include_once DEMETER;

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table color</h2>';

$db->query("DROP TABLE IF EXISTS `color`");
$db->query("CREATE TABLE IF NOT EXISTS `color` (
	`id` int(11) unsigned NOT NULL,

	`alive` tinyint(4) NOT NULL DEFAULT 0,
	`isWinner` tinyint(4) NOT NULL DEFAULT 0,
	`credits` int(20) unsigned NOT NULL DEFAULT 0,
	`players` int(5) unsigned NOT NULL DEFAULT 0,
	`activePlayers` int(5) unsigned NOT NULL DEFAULT 0,
	`points` int(11) unsigned NOT NULL DEFAULT 0,
	`sectors` smallint(6) unsigned NOT NULL DEFAULT 0,
	`electionStatement` tinyint(4) NOT NULL DEFAULT 0,
	`isClosed` tinyint(4) NOT NULL DEFAULT 1,

	`dLastElection` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

echo '<h3>Remplissage de la table color</h3>';
$qr = $db->prepare("INSERT INTO `color` (`id`, `alive`, `credits`, `players`, `activePlayers`, `points`, `sectors`, `electionStatement`, `isClosed`, `dLastElection`) VALUES
(0, 0, 0, 0, 0, 0, 0, 1, 0, ?),
(1, 1, 0, 0, 0, 0, 0, 1, 0, ?),
(2, 1, 0, 0, 0, 0, 0, 1, 0, ?),
(3, 1, 0, 0, 0, 0, 0, 1, 0, ?),
(4, 1, 0, 0, 0, 0, 0, 1, 0, ?),
(5, 1, 0, 0, 0, 0, 0, 1, 0, ?),
(6, 1, 0, 0, 0, 0, 0, 1, 0, ?),
(7, 1, 0, 0, 0, 0, 0, 1, 0, ?);");
$date = Utils::addSecondsToDate(Utils::now(), - 500000);
$qr->execute(array($date, $date, $date, $date, $date, $date, $date, $date));

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table factionNews</h2>';

$db->query("DROP TABLE IF EXISTS `factionNews`");
$db->query("CREATE TABLE IF NOT EXISTS `factionNews` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rFaction` int(11) unsigned NOT NULL,

	`title` varchar(255) NOT NULL DEFAULT 'Nouvelle',
	`oContent` text NOT NULL,
	`pContent` text NOT NULL,
	`pinned` tinyint(3) NOT NULL DEFAULT 0,
	`statement` tinyint(3) NOT NULL DEFAULT 1,

	`dCreation` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkFactionNewsFaction FOREIGN KEY (rFaction) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table player</h2>';

$db->query("DROP TABLE IF EXISTS `player`");
$db->query("CREATE TABLE IF NOT EXISTS `player` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rColor` int(10) unsigned NOT NULL,
	`rGodfather` int(10) unsigned NOT NULL,

	`bind` varchar(50) default NULL,
	`name` varchar(25) NOT NULL,
	`avatar` varchar(12) NOT NULL,
	`sex` tinyint(4) NOT NULL DEFAULT 1,
	`status` smallint(6) unsigned NOT NULL DEFAULT 1,
	`credit` bigint(20) unsigned NOT NULL DEFAULT 0,
	`experience` bigint(20) unsigned NOT NULL DEFAULT 0,
	`factionPoint` int(11) unsigned NOT NULL DEFAULT 0,
	`level` tinyint(4) unsigned DEFAULT NULL DEFAULT 0,
	`victory` int(10) unsigned DEFAULT NULL DEFAULT 0,
	`defeat` int(10) unsigned DEFAULT NULL DEFAULT 0,
	`premium` tinyint(4) NOT NULL DEFAULT 0,
	`statement` tinyint(4) NOT NULL DEFAULT 0,
	`description` text DEFAULT NULL,

	`stepTutorial` tinyint(4) unsigned DEFAULT NULL,
	`stepDone` tinyint(4) unsigned NOT NULL DEFAULT 0,

	`iUniversity` int(10) unsigned NOT NULL DEFAULT 0,
	`partNaturalSciences` int(10) unsigned NOT NULL DEFAULT 0,
	`partLifeSciences` int(10) unsigned NOT NULL DEFAULT 0,
	`partSocialPoliticalSciences` int(10) unsigned NOT NULL DEFAULT 0,
	`partInformaticEngineering` int(10) unsigned NOT NULL DEFAULT 0,

	`dInscription` datetime DEFAULT NULL,
	`dLastConnection` datetime DEFAULT NULL,
	`dLastActivity` datetime DEFAULT NULL,
	`uPlayer` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	UNIQUE KEY `name_UNIQUE` (`name`),
	CONSTRAINT fkPlayerColor FOREIGN KEY (rColor) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT = 1;");

#--------------------------------------------------------------------------------------------
echo '<h3>Ajout du Joueur Gaia</h3>';

$p = new Player();
$p->bind = Utils::generateString(25);
$p->rColor = 0;
$p->name = 'Rebelle';
$p->avatar = '000-1';
$p->status = 1;
$p->credit = 10000000;
$p->uPlayer = Utils::now();
$p->experience = 15000;
$p->factionPoint = 0;
$p->level = 5;
$p->victory = 0;
$p->defeat = 0;
$p->stepTutorial = 0;
$p->stepDone = 0;
$p->iUniversity = 0;
$p->partNaturalSciences = 25;
$p->partLifeSciences = 25;
$p->partSocialPoliticalSciences = 25;
$p->partInformaticEngineering = 25;
$p->dInscription = Utils::now();
$p->dLastConnection = Utils::now();
$p->dLastActivity = Utils::now();
$p->premium = 0;
$p->statement = PAM_DEAD;
ASM::$pam->add($p);

$p = new Player();
$p->bind = Utils::generateString(25);
$p->rColor = 0;
$p->name = 'Jean-Mi';
$p->avatar = '059-1';
$p->status = 1;
$p->credit = 10000000;
$p->uPlayer = Utils::now();
$p->experience = 15000;
$p->factionPoint = 0;
$p->level = 5;
$p->victory = 0;
$p->defeat = 0;
$p->stepTutorial = 0;
$p->stepDone = 0;
$p->iUniversity = 0;
$p->partNaturalSciences = 25;
$p->partLifeSciences = 25;
$p->partSocialPoliticalSciences = 25;
$p->partInformaticEngineering = 25;
$p->dInscription = Utils::now();
$p->dLastConnection = Utils::now();
$p->dLastActivity = Utils::now();
$p->premium = 0;
$p->statement = PAM_DEAD;
ASM::$pam->add($p);

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table sector</h2>';

$db->query("DROP TABLE IF EXISTS `sector`");
$db->query("CREATE TABLE IF NOT EXISTS `sector` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rColor` int(11) unsigned NOT NULL,
	`rSurrender` int(11) unsigned DEFAULT NULL,

	`xPosition` smallint(5) unsigned DEFAULT NULL,
	`yPosition` smallint(5) unsigned DEFAULT NULL,
	`xBarycentric` smallint(5) unsigned NOT NULL DEFAULT 0,
	`yBarycentric` smallint(5) unsigned NOT NULL DEFAULT 0,
	`tax` smallint(5) unsigned NOT NULL DEFAULT 0,
	`population` int(11) unsigned NOT NULL,
	`lifePlanet` int(11) unsigned DEFAULT NULL,
	`name` varchar(255) DEFAULT NULL,
	`prime` tinyint(1) NOT NULL DEFAULT 0,

	PRIMARY KEY (`id`),
	CONSTRAINT fkSectorColor FOREIGN KEY (rColor) REFERENCES color(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<h3>Ajout du trigger sector</h3>';

$db->query("DROP TRIGGER IF EXISTS `saveSectorChange`;");
$db->query("CREATE TRIGGER `saveSectorChange` BEFORE UPDATE ON `sector`
 FOR EACH ROW BEGIN
	IF NEW.rColor != OLD.rColor THEN
	INSERT INTO changeColorSector(
		rSector,
		oldColor,
		newColor,
		dChangement)
	VALUES(
		OLD.id,
		OLD.rColor,
		NEW.rColor,
		NOW());
	END IF;
END;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table system</h2>';

$db->query("DROP TABLE IF EXISTS `system`");
$db->query("CREATE TABLE IF NOT EXISTS `system` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rSector` int(11) unsigned NOT NULL,
	`rColor` int(11) unsigned NOT NULL,

	`xPosition` smallint(5) unsigned DEFAULT NULL,
	`yPosition` smallint(5) unsigned DEFAULT NULL,
	`typeOfSystem` smallint(5) unsigned DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkSystemSector FOREIGN KEY (rSector) REFERENCES sector(id),
	CONSTRAINT fkSystemColor FOREIGN KEY (rColor) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

echo '<h3>Ajout du trigger system</h3>';

$db->query("DROP TRIGGER IF EXISTS `saveSystemChange`;");
$db->query("CREATE TRIGGER `saveSystemChange` BEFORE UPDATE ON `system`
 FOR EACH ROW BEGIN
	IF NEW.rColor != OLD.rColor THEN
	INSERT INTO changeColorSystem(
		rSystem,
		oldColor,
		newColor,
		dChangement)
	VALUES(
		OLD.id,
		OLD.rColor,
		NEW.rColor,
		NOW());
	END IF;
END;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table place</h2>';

$db->query("DROP TABLE IF EXISTS `place`");
$db->query("CREATE TABLE IF NOT EXISTS `place` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rSystem` int(11) unsigned NOT NULL,

	`typeOfPlace` tinyint(3) unsigned NOT NULL,
	`position` tinyint(3) unsigned NOT NULL,

	`population` float unsigned NOT NULL,
	`coefResources` float unsigned NOT NULL,
	`coefHistory` float unsigned NOT NULL,
	
	`resources` bigint(20) unsigned DEFAULT 0,

	`uPlace` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkPlaceSystem FOREIGN KEY (rSystem) REFERENCES system(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

echo '<h3>Ajout du trigger place</h3>';

$db->query("DROP TRIGGER IF EXISTS `savePlaceChange`;");
$db->query("CREATE TRIGGER `savePlaceChange` BEFORE UPDATE ON `place`
 FOR EACH ROW BEGIN
	IF NEW.rPlayer != OLD.rPlayer THEN
	INSERT INTO changeColorPlace(
		rPlace,
		oldPlayer,
		newPlayer,
		dChangement)
	VALUES(
		OLD.id,
		OLD.rPlayer,
		NEW.rPlayer,
		NOW());
	END IF;
END;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table changeColorPlace</h2>';

$db->query("DROP TABLE IF EXISTS `changeColorPlace`");
$db->query("CREATE TABLE IF NOT EXISTS `changeColorPlace` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlace` int(11) unsigned NOT NULL,
	`oldPlayer` tinyint(3) unsigned NOT NULL,
	`newPlayer` tinyint(3) unsigned NOT NULL,
	`dChangement` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table changeColorSector</h2>';

$db->query("DROP TABLE IF EXISTS `changeColorSector`");
$db->query("CREATE TABLE IF NOT EXISTS `changeColorSector` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rSector` int(11) unsigned NOT NULL,
	`oldColor` tinyint(3) unsigned NOT NULL,
	`newColor` tinyint(3) unsigned NOT NULL,
	`dChangement` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table changeColorSystem</h2>';

$db->query("DROP TABLE IF EXISTS `changeColorSystem`");
$db->query("CREATE TABLE IF NOT EXISTS `changeColorSystem` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rSystem` int(11) unsigned NOT NULL,
	`oldColor` tinyint(3) unsigned NOT NULL,
	`newColor` tinyint(3) unsigned NOT NULL,
	`dChangement` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h3>Ajout des trois vues</h3>';

$db->query("DROP VIEW IF EXISTS `vGalaxyDiary`;");
$db->query("CREATE VIEW `vGalaxyDiary` AS select `h`.`id` AS `id`,`h`.`rSector` AS `sector`,`h`.`oldColor` AS `oldColor`,`h`.`newColor` AS `newColor`,`h`.`dChangement` AS `dChangement` from (`changeColorSector` `h` left join `system` `s` on((`h`.`rSector` = `s`.`id`))) order by `h`.`dChangement` desc limit 0,100;");

$db->query("DROP VIEW IF EXISTS `vSectorDiary`;");
$db->query("CREATE VIEW `vSectorDiary` AS select count(`h`.`id`) AS `occurency`,`h`.`id` AS `id`,`h`.`rSystem` AS `system`,`s`.`rSector` AS `sector`,`h`.`oldColor` AS `oldColor`,`h`.`newColor` AS `newColor`,`h`.`dChangement` AS `dChangement` from (((`changeColorSystem` `h` left join `system` `s` on((`h`.`rSystem` = `s`.`id`))) left join `color` `c1` on((`h`.`oldColor` = `c1`.`id`))) left join `color` `c2` on((`h`.`newColor` = `c2`.`id`))) group by `c1`.`id`,`c2`.`id`,hour(`h`.`dChangement`) order by `h`.`dChangement` desc limit 0,1000;");

$db->query("DROP VIEW IF EXISTS `vSystemDiary`;");
$db->query("CREATE VIEW `vSystemDiary` AS select `h`.`id` AS `id`,`h`.`rPlace` AS `place`,`h`.`oldPlayer` AS `oldPlayer`,`p1`.`name` AS `oldName`,`c1`.`id` AS `oldColor`,`h`.`newPlayer` AS `newPlayer`,`p2`.`name` AS `newName`,`c2`.`id` AS `newColor`,`h`.`dChangement` AS `dChangement`,`p`.`position` AS `position`,`p`.`rSystem` AS `system` from (((((`changeColorPlace` `h` left join `place` `p` on((`p`.`id` = `h`.`rPlace`))) left join `player` `p1` on((`h`.`oldPlayer` = `p1`.`id`))) left join `color` `c1` on((`p1`.`rColor` = `c1`.`id`))) left join `player` `p2` on((`h`.`newPlayer` = `p2`.`id`))) left join `color` `c2` on((`p2`.`rColor` = `c2`.`id`))) order by `h`.`dChangement` desc limit 0,5000;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table orbitalBase</h2>';

$db->query("DROP TABLE IF EXISTS `orbitalBase`");
$db->query("CREATE TABLE IF NOT EXISTS `orbitalBase` (
	`rPlace` int(11) unsigned NOT NULL,
	`rPlayer` int(11) unsigned NOT NULL,

	`name` varchar(45) COLLATE utf8_bin NOT NULL,
	`typeOfBase` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`levelGenerator` tinyint(3) unsigned DEFAULT 0,
	`levelRefinery` tinyint(3) unsigned DEFAULT 0,
	`levelDock1` tinyint(3) unsigned DEFAULT 0,
	`levelDock2` tinyint(3) unsigned DEFAULT 0,
	`levelDock3` tinyint(3) unsigned DEFAULT 0,
	`levelTechnosphere` tinyint(3) unsigned DEFAULT 0,
	`levelCommercialPlateforme` tinyint(3) unsigned DEFAULT 0,
	`levelStorage` tinyint(3) unsigned DEFAULT 0,
	`levelRecycling` tinyint(3) unsigned DEFAULT 0,
	`levelSpatioport` tinyint(3) unsigned DEFAULT 0,
	`points` int(10) unsigned DEFAULT 0,

	`iSchool` int(10) unsigned DEFAULT 0,
	`iAntiSpy` int(11) unsigned DEFAULT 0,
	`antiSpyAverage` int(10) unsigned DEFAULT 0,

	`pegaseStorage` smallint(5) unsigned DEFAULT 0,
	`satyreStorage` smallint(5) unsigned DEFAULT 0,
	`sireneStorage` smallint(5) unsigned DEFAULT 0,
	`dryadeStorage` smallint(5) unsigned DEFAULT 0,
	`chimereStorage` smallint(5) unsigned DEFAULT 0,
	`meduseStorage` smallint(5) unsigned DEFAULT 0,
	`griffonStorage` smallint(5) unsigned DEFAULT 0,
	`cyclopeStorage` smallint(5) unsigned DEFAULT 0,
	`minotaureStorage` smallint(5) unsigned DEFAULT 0,
	`hydreStorage` smallint(5) unsigned DEFAULT 0,
	`cerbereStorage` smallint(5) unsigned DEFAULT 0,
	`phenixStorage` smallint(5) unsigned DEFAULT 0,

	`motherShip` tinyint(1) DEFAULT 0,
	`resourcesStorage` bigint(20) unsigned DEFAULT 0,

	`uOrbitalBase` datetime DEFAULT NULL,
	`dCreation` datetime DEFAULT NULL,
	
	PRIMARY KEY (`rPlace`),
	CONSTRAINT fkOrbitalBasePlace FOREIGN KEY (rPlace) REFERENCES place(id),
	CONSTRAINT fkOrbitalBasePlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commercialRoute</h2>';

$db->query("DROP TABLE IF EXISTS `commercialRoute`");
$db->query("CREATE TABLE IF NOT EXISTS `commercialRoute` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rOrbitalBase` int(11) unsigned NOT NULL,
	`rOrbitalBaseLinked` int(11) unsigned NOT NULL,

	`imageLink` varchar(10) NOT NULL,
	`distance` int(10) unsigned NOT NULL,
	`price` int(10) unsigned NOT NULL,
	`income` int(10) unsigned NOT NULL,

	`dProposition` datetime DEFAULT NULL,
	`dCreation` datetime DEFAULT NULL,
	`statement` tinyint(3) unsigned NOT NULL COMMENT '0 = pas acceptée, 1 = active',

	PRIMARY KEY (`id`),
	CONSTRAINT fkCommercialRouteOrbitalBaseA FOREIGN KEY (rOrbitalBase) REFERENCES orbitalBase(rPlace),
	CONSTRAINT fkCommercialRouteOrbitalBaseB FOREIGN KEY (rOrbitalBaseLinked) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table orbitalBaseBuildingQueue</h2>';

$db->query("DROP TABLE IF EXISTS `orbitalBaseBuildingQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `orbitalBaseBuildingQueue` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rOrbitalBase` int(11) unsigned NOT NULL,

	`buildingNumber` tinyint(3) unsigned NOT NULL,
	`targetLevel` tinyint(3) unsigned NOT NULL,
	`dStart` datetime NOT NULL,
	`dEnd` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkOrbitalBaseBuildingQueueOrbitalBase FOREIGN KEY (rOrbitalBase) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table orbitalBaseShipQueue</h2>';

$db->query("DROP TABLE IF EXISTS `orbitalBaseShipQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `orbitalBaseShipQueue` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rOrbitalBase` int(11) unsigned NOT NULL,

	`dockType` tinyint(3) unsigned NOT NULL,
	`shipNumber` tinyint(3) unsigned NOT NULL,
	`quantity` int(10) unsigned NOT NULL,

	`dStart` datetime NOT NULL,
	`dEnd` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkOrbitalBaseShipQueueOrbitalBase FOREIGN KEY (rOrbitalBase) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table transaction</h2>';

$db->query("DROP TABLE IF EXISTS `transaction`");
$db->query("CREATE TABLE IF NOT EXISTS `transaction` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rPlace` int(11) unsigned NOT NULL,

	`type` tinyint(4) NOT NULL COMMENT '0 = resource, 1 = ship, 2 = commander',
	`quantity` int(11) NOT NULL,
	`identifier` int(11) DEFAULT NULL,
	`price` int(11) NOT NULL,
	`commercialShipQuantity` int(11) NOT NULL,
	`statement` tinyint(4) NOT NULL COMMENT '0 = proposed, 1 = completed, 2 = canceled',

	`dPublication` datetime NOT NULL,
	`dValidation` datetime DEFAULT NULL,
	`currentRate` float DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkTransactionPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<h3>Remplissage de la table transaction</h3>';

$qr = $db->prepare("INSERT INTO `transaction` (`rPlayer`, `rPlace`, `type`, `quantity`, `identifier`, `price`, `commercialShipQuantity`, `statement`, `dPublication`, `dValidation`, `currentRate`) VALUES
(1, 0, ?, 8, NULL, 10, 0, ?, ?, ?, ?),
(1, 0, ?, 1, NULL, 12, 0, ?, ?, ?, ?),
(1, 0, ?, 8, NULL, 15, 0, ?, ?, ?, ?);");
$qr->execute(array(Transaction::TYP_RESOURCE, Transaction::ST_COMPLETED, Utils::now(), Utils::now(), 1.26,
	Transaction::TYP_COMMANDER, Transaction::ST_COMPLETED, Utils::now(), Utils::now(), 12,
	Transaction::TYP_SHIP, Transaction::ST_COMPLETED, Utils::now(), Utils::now(), 1.875));

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commercialShipping</h2>';

$db->query("DROP TABLE IF EXISTS `commercialShipping`");
$db->query("CREATE TABLE IF NOT EXISTS `commercialShipping` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rBase` int(11) unsigned NOT NULL,
	`rBaseDestination` int(11) unsigned NOT NULL,
	`rTransaction` int(11) unsigned DEFAULT NULL,

	`resourceTransported` int(11) DEFAULT NULL,
	`shipQuantity` int(11) NOT NULL,

	`dDeparture` datetime NOT NULL,
	`dArrival` datetime NOT NULL,
	`statement` smallint(6) NOT NULL COMMENT '0 = prêt au départ, 1 = aller, 2 = retour',

	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commercialTax</h2>';

$db->query("DROP TABLE IF EXISTS `commercialTax`");
$db->query("CREATE TABLE IF NOT EXISTS `commercialTax` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`faction` smallint(6) NOT NULL,
	`relatedFaction` smallint(6) NOT NULL,
	`exportTax` float NOT NULL,
	`importTax` float NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<h3>Remplissage de la table commercialTax</h3>';
$qr = $db->prepare("INSERT INTO `commercialTax` (`faction`, `relatedFaction`, `exportTax`, `importTax`) VALUES
(1, 1, 5, 5),
(1, 2, 5, 5),
(1, 3, 5, 5),
(1, 4, 5, 5),
(1, 5, 5, 5),
(1, 6, 5, 5),
(1, 7, 5, 5),

(2, 1, 5, 5),
(2, 2, 5, 5),
(2, 3, 5, 5),
(2, 4, 5, 5),
(2, 5, 5, 5),
(2, 6, 5, 5),
(2, 7, 5, 5),

(3, 1, 5, 5),
(3, 2, 5, 5),
(3, 3, 5, 5),
(3, 4, 5, 5),
(3, 5, 5, 5),
(3, 6, 5, 5),
(3, 7, 5, 5),

(4, 1, 5, 5),
(4, 2, 5, 5),
(4, 3, 5, 5),
(4, 4, 5, 5),
(4, 5, 5, 5),
(4, 6, 5, 5),
(4, 7, 5, 5),

(5, 1, 5, 5),
(5, 2, 5, 5),
(5, 3, 5, 5),
(5, 4, 5, 5),
(5, 5, 5, 5),
(5, 6, 5, 5),
(5, 7, 5, 5),

(6, 1, 5, 5),
(6, 2, 5, 5),
(6, 3, 5, 5),
(6, 4, 5, 5),
(6, 5, 5, 5),
(6, 6, 5, 5),
(6, 7, 5, 5),

(7, 1, 5, 5),
(7, 2, 5, 5),
(7, 3, 5, 5),
(7, 4, 5, 5),
(7, 5, 5, 5),
(7, 6, 5, 5),
(7, 7, 5, 5);");
$qr->execute();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table law</h2>';

$db->query("DROP TABLE IF EXISTS `law`");
$db->query("CREATE TABLE IF NOT EXISTS `law` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rColor` int(10) unsigned NOT NULL,

	`type` int(11) NOT NULL,
	`statement` int(11) NOT NULL,
	`options` text DEFAULT NULL,

	`dEnd` datetime DEFAULT NULL,
	`dEndVotation` datetime DEFAULT NULL,
	`dCreation` datetime DEFAULT NULL,

	PRIMARY KEY `id` (`id`),
	CONSTRAINT fkLawColor FOREIGN KEY (rColor) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table voteLaw</h2>';

$db->query("DROP TABLE IF EXISTS `voteLaw`");
$db->query("CREATE TABLE IF NOT EXISTS `voteLaw` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,

	`rLaw` int(11) unsigned NOT NULL,
	`rPlayer` int(11) unsigned NOT NULL,

	`vote` tinyint(4) NOT NULL,
	`dVotation` date NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkVoteLawLaw FOREIGN KEY (rLaw) REFERENCES law(id),
	CONSTRAINT fkVoteLawPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table forumTopic</h2>';

$db->query("DROP TABLE IF EXISTS `forumTopic`");
$db->query("CREATE TABLE IF NOT EXISTS `forumTopic` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rColor` int(11) unsigned NOT NULL,
	`rPlayer` int(11) unsigned NOT NULL,
	`rForum` int(11) unsigned NOT NULL,

	`title` varchar(255) NOT NULL,
	`isClosed` tinyint(4) NOT NULL DEFAULT 0,
	`isArchived` tinyint(4) NOT NULL DEFAULT 0,
	`isUp` tinyint(4) NOT NULL DEFAULT 0,

	`dCreation` datetime NOT NULL,
	`dLastMessage` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkForumTopicColor FOREIGN KEY (rColor) REFERENCES color(id),
	CONSTRAINT fkForumTopicPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table forumMessage</h2>';

$db->query("DROP TABLE IF EXISTS `forumMessage`");
$db->query("CREATE TABLE IF NOT EXISTS `forumMessage` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rTopic` int(11) unsigned NOT NULL,

	`oContent` text NOT NULL,
	`pContent` text NOT NULL,

	`statement` int(11) NOT NULL DEFAULT 0,

	`dCreation` datetime NOT NULL,
	`dLastModification` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkForumMessagePlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkForumMessageTopic FOREIGN KEY (rTopic) REFERENCES forumTopic(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table forumLastView</h2>';

$db->query("DROP TABLE IF EXISTS `forumLastView`");
$db->query("CREATE TABLE IF NOT EXISTS `forumLastView` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rTopic` int(11) unsigned NOT NULL,

	`dView` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkForumLastViewsPlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkForumLastViewsTopic FOREIGN KEY (rTopic) REFERENCES forumTopic(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table election</h2>';

$db->query("DROP TABLE IF EXISTS `election`");
$db->query("CREATE TABLE IF NOT EXISTS `election` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rColor` int(11) unsigned NOT NULL,

	`dElection` datetime NOT NULL,
	PRIMARY KEY (`id`),
	CONSTRAINT fkElectionColor FOREIGN KEY (rColor) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table candidate</h2>';

$db->query("DROP TABLE IF EXISTS `candidate`");
$db->query("CREATE TABLE IF NOT EXISTS `candidate` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rElection` int(11) unsigned NOT NULL,
	`rPlayer` int(11) unsigned NOT NULL,

	`chiefChoice` tinyint(4) unsigned DEFAULT 1,
	`treasurerChoice` tinyint(4) unsigned DEFAULT 1,
	`warlordChoice` tinyint(4) unsigned DEFAULT 1,
	`ministerChoice` tinyint(4) unsigned DEFAULT 1,

	`program` text,
	`dPresentation` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkCandidateElection FOREIGN KEY (rElection) REFERENCES election(id),
	CONSTRAINT fkCandidatePlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table vote</h2>';

$db->query("DROP TABLE IF EXISTS `vote`");
$db->query("CREATE TABLE IF NOT EXISTS `vote` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rCandidate` int(11) unsigned NOT NULL,
	`rPlayer` int(11) unsigned NOT NULL,
	`rElection` int(11) unsigned NOT NULL,

	`dVotation` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkVoteCandidate FOREIGN KEY (rCandidate) REFERENCES candidate(id),
	CONSTRAINT fkVotePlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkVoteElection FOREIGN KEY (rElection) REFERENCES election(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commander</h2>';

$db->query("DROP TABLE IF EXISTS `commander`");
$db->query("CREATE TABLE IF NOT EXISTS `commander` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rBase` int(11) unsigned NOT NULL,

	`name` varchar(45) NOT NULL,
	`comment` text,
	`sexe` tinyint(4) NOT NULL DEFAULT 1,
	`age` int(10) unsigned NOT NULL DEFAULT 20,
	`avatar` varchar(20) NOT NULL,
	`level` tinyint(4) unsigned NOT NULL DEFAULT 1,
	`experience` int(10) unsigned NOT NULL DEFAULT 1,
	`palmares` int(10) unsigned NOT NULL DEFAULT 0,
	`statement` tinyint(4) unsigned NOT NULL DEFAULT 0,
	`line` tinyint(4) unsigned DEFAULT NULL,
	`rStartPlace` int(11) unsigned DEFAULT NULL,
	`rDestinationPlace` int(11) unsigned DEFAULT NULL,
	`dStart` datetime DEFAULT NULL,
	`dArrival` datetime DEFAULT NULL,
	`resources` int(11)unsigned NOT NULL DEFAULT 0,
	`travelType` tinyint(4) unsigned DEFAULT NULL,
	`travelLength` tinyint(4) unsigned DEFAULT NULL,

	`dCreation` datetime DEFAULT NULL,
	`dAffectation` datetime DEFAULT NULL,
	`dDeath` datetime DEFAULT NULL,
	`uCommander` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkCommanderPlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkCommanderBase FOREIGN KEY (rBase) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table squadron</h2>';

$db->query("DROP TABLE IF EXISTS `squadron`");
$db->query("CREATE TABLE IF NOT EXISTS `squadron` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rCommander` int(11) unsigned NOT NULL,

	`ship0` tinyint(4) unsigned DEFAULT 0,
	`ship1` tinyint(4) unsigned DEFAULT 0,
	`ship2` tinyint(4) unsigned DEFAULT 0,
	`ship3` tinyint(4) unsigned DEFAULT 0,
	`ship4` tinyint(4) unsigned DEFAULT 0,
	`ship5` tinyint(4) unsigned DEFAULT 0,
	`ship6` tinyint(4) unsigned DEFAULT 0,
	`ship7` tinyint(4) unsigned DEFAULT 0,
	`ship8` tinyint(4) unsigned DEFAULT 0,
	`ship9` tinyint(4) unsigned DEFAULT 0,
	`ship10` tinyint(4) unsigned DEFAULT 0,
	`ship11` tinyint(4) unsigned DEFAULT 0,

	`dCreation` datetime DEFAULT NULL,
	`dLastModification` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkSquadronCommander FOREIGN KEY (rCommander) REFERENCES commander(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table report</h2>';

$db->query("DROP TABLE IF EXISTS `report`");
$db->query("CREATE TABLE IF NOT EXISTS `report` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayerAttacker` int(11) unsigned NOT NULL,
	`rPlayerDefender` int(11) unsigned NOT NULL,
	`rPlayerWinner` int(11) unsigned NOT NULL,

	`resources` int(11) unsigned NOT NULL,
	`expCom` int(11) unsigned NOT NULL,
	`rPlace` int(10) unsigned NOT NULL,
	`placeName` varchar(45) NOT NULL,
	`type` tinyint(4) unsigned DEFAULT NULL,
	`isLegal` tinyint(4) unsigned DEFAULT 1,
	`hasBeenPunished` tinyint(4) unsigned DEFAULT 0,
	`round` int(11) unsigned DEFAULT 0,
	`importance` int(11) unsigned DEFAULT NULL,
	`pevInBeginA` smallint(6) unsigned,
	`pevInBeginD` smallint(6) unsigned,
	`pevAtEndA` smallint(6) unsigned,
	`pevAtEndD` smallint(6) unsigned,

	`avatarA` varchar(255) NOT NULL,
	`avatarD` varchar(255) NOT NULL,
	`nameA` varchar(255) NOT NULL,
	`nameD` varchar(255) NOT NULL,
	`levelA` int(10) unsigned NOT NULL,
	`levelD` int(10) unsigned NOT NULL,
	`experienceA` int(10) unsigned NOT NULL,
	`experienceD` int(10) unsigned NOT NULL,
	`palmaresA` int(10) unsigned NOT NULL,
	`palmaresD` int(10) unsigned NOT NULL,
	`expPlayerA` int(10) unsigned NOT NULL,
	`expPlayerD` int(10) unsigned NOT NULL,
	`statementAttacker` tinyint(4) unsigned NOT NULL DEFAULT 0,
	`statementDefender` tinyint(4) unsigned NOT NULL DEFAULT 0,

	`dFight` datetime DEFAULT NULL,
	PRIMARY KEY (`id`),
	CONSTRAINT fkReportPlayerA FOREIGN KEY (rPlayerAttacker) REFERENCES player(id),
	CONSTRAINT fkReportPlayerD FOREIGN KEY (rPlayerDefender) REFERENCES player(id),
	CONSTRAINT fkReportPlayerW FOREIGN KEY (rPlayerWinner) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table squadronReport</h2>';

$db->query("DROP TABLE IF EXISTS `squadronReport`");
$db->query("CREATE TABLE IF NOT EXISTS `squadronReport` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rReport` int(11) unsigned NOT NULL,
	`rCommander` int(11) unsigned NULL,

	`position` tinyint(4) unsigned NOT NULL,
	`round` int(11) NOT NULL,

	`ship0` tinyint(4) unsigned NOT NULL,
	`ship1` tinyint(4) unsigned NOT NULL,
	`ship2` tinyint(4) unsigned NOT NULL,
	`ship3` tinyint(4) unsigned NOT NULL,
	`ship4` tinyint(4) unsigned NOT NULL,
	`ship5` tinyint(4) unsigned NOT NULL,
	`ship6` tinyint(4) unsigned NOT NULL,
	`ship7` tinyint(4) unsigned NOT NULL,
	`ship8` tinyint(4) unsigned NOT NULL,
	`ship9` tinyint(4) unsigned NOT NULL,
	`ship10` tinyint(4) unsigned NOT NULL,
	`ship11` tinyint(4) unsigned NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkSquadronReportReport FOREIGN KEY (rReport) REFERENCES report(id),
	CONSTRAINT fkSquadronReportCommander FOREIGN KEY (rCommander) REFERENCES commander(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table spyReport</h2>';

$db->query("DROP TABLE IF EXISTS `spyReport`");
$db->query("CREATE TABLE IF NOT EXISTS `spyReport` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rPlace` int(11) unsigned NOT NULL,
	
	`price` int(11) unsigned NOT NULL,
	`placeColor` smallint(6) unsigned NOT NULL,
	`typeOfBase` tinyint(4) unsigned NOT NULL,
	`typeOfOrbitalBase` tinyint(4) unsigned NOT NULL,
	`placeName` varchar(255) NOT NULL,
	`points` int(11) unsigned NOT NULL,
	`rEnemy` int(11) unsigned NOT NULL,
	`enemyName` varchar(255) NOT NULL,
	`enemyAvatar` varchar(255) NOT NULL,
	`enemyLevel` int(11) unsigned NOT NULL,
	`resources` int(11) unsigned NOT NULL,
	`shipsInStorage` text NOT NULL,
	`antiSpyInvest` int(11) NOT NULL,
	`commercialRouteIncome` int(11) NOT NULL,
	`commanders` text NOT NULL,
	`success` smallint(6) unsigned NOT NULL,
	`type` tinyint(4) unsigned NOT NULL,

	`dSpying` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkSpyReportPlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkSpyReportPlace FOREIGN KEY (rPlace) REFERENCES place(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table ranking</h2>';

$qr = $db->prepare("DROP TABLE IF EXISTS `ranking`");
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `ranking` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`dRanking` datetime NOT NULL,
	`player` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`faction` tinyint(1) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table playerRanking</h2>';

$qr = $db->prepare("DROP TABLE IF EXISTS `playerRanking`");
$qr->execute();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `playerRanking` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rRanking` int(11) unsigned NOT NULL,
	`rPlayer` int(11) unsigned NOT NULL,

	`general` int(11) unsigned NOT NULL,
	`generalPosition` smallint(6) NOT NULL,
	`generalVariation` smallint(6) NOT NULL,

	`experience` int(11) unsigned NOT NULL,
	`experiencePosition` smallint(6) NOT NULL,
	`experienceVariation` smallint(6) NOT NULL,

	`butcher` int(11) unsigned NOT NULL,
	`butcherDestroyedPEV` int(11) unsigned NOT NULL,
	`butcherLostPEV` int(11) unsigned NOT NULL,
	`butcherPosition` smallint(6) NOT NULL,
	`butcherVariation` smallint(6) NOT NULL,

	`armies` int(11) unsigned NOT NULL,
	`armiesPosition` smallint(6) NOT NULL,
	`armiesVariation` smallint(6) NOT NULL,

	`trader` int(11) unsigned NOT NULL,
	`traderPosition` smallint(6) NOT NULL,
	`traderVariation` smallint(6) NOT NULL,

	`resources` int(11) unsigned NOT NULL,
	`resourcesPosition` smallint(6) NOT NULL,
	`resourcesVariation` smallint(6) NOT NULL,

	`fight` int(11) unsigned NOT NULL,
	`victories` int(11) unsigned NOT NULL,
	`defeat` int(11) unsigned NOT NULL,
	`fightPosition` smallint(6) NOT NULL,
	`fightVariation` smallint(6) NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkPlayerRankingRanking FOREIGN KEY (rRanking) REFERENCES ranking(id),
	CONSTRAINT fkPlayerRankingPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table factionRanking</h2>';

$qr = $db->prepare("DROP TABLE IF EXISTS `factionRanking`");
$qr->execute();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `factionRanking` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rRanking` int(11) unsigned NOT NULL,
	`rFaction` int(11) unsigned NOT NULL,

	`general` int(11) unsigned NOT NULL,
	`generalPosition` smallint(6) NOT NULL,
	`generalVariation` smallint(6) NOT NULL,

	`wealth` int(11) unsigned NOT NULL,
	`wealthPosition` smallint(6) NOT NULL,
	`wealthVariation` smallint(6) NOT NULL,

	`territorial` int(11) unsigned NOT NULL,
	`territorialPosition` smallint(6) NOT NULL,
	`territorialVariation` smallint(6) NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkFactionRankingRanking FOREIGN KEY (rRanking) REFERENCES ranking(id),
	CONSTRAINT fkFactionRankingPlayer FOREIGN KEY (rFaction) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table message</h2>';

$db->query("DROP TABLE IF EXISTS `message`");
$db->query("CREATE TABLE IF NOT EXISTS `message` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`thread` int(11) unsigned DEFAULT NULL,
	`rPlayerWriter` int(11) unsigned DEFAULT NULL,
	`rPlayerReader` int(11) unsigned NOT NULL,
	`dSending` datetime NOT NULL,
	`content` text NOT NULL,
	`readed` tinyint(1) DEFAULT 0,
	`writerStatement` tinyint(1) DEFAULT 1,
	`readerStatement` tinyint(1) DEFAULT 1,

	PRIMARY KEY (`id`),
	CONSTRAINT fkMessagePlayerA FOREIGN KEY (rPlayerWriter) REFERENCES player(id),
	CONSTRAINT fkMessagePlayerB FOREIGN KEY (rPlayerReader) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table notification</h2>';

$db->query("DROP TABLE IF EXISTS `notification`");
$db->query("CREATE TABLE IF NOT EXISTS `notification` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`title` varchar(100) NOT NULL,
	`content` text,
	`dSending` datetime NOT NULL,
	`readed` tinyint(1) DEFAULT 0,
	`archived` tinyint(1) DEFAULT 0,

	PRIMARY KEY (`id`),
	CONSTRAINT fkNotificationPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table roadMap</h2>';

$db->query("DROP TABLE IF EXISTS `roadMap`");
$db->query("CREATE TABLE IF NOT EXISTS `roadMap` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) NOT NULL,
	`oContent` text NOT NULL,
	`pContent` text NOT NULL,
	`statement` tinyint(4) NOT NULL COMMENT '0 = caché, 1 = affiché',
	`dCreation` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table research</h2>';

$db->query("DROP TABLE IF EXISTS `research`");
$db->query("CREATE TABLE IF NOT EXISTS `research` (
	`rPlayer` int(11) unsigned NOT NULL,

	`mathLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`physLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`chemLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`bioLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`mediLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`econoLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`psychoLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`networkLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`algoLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`statLevel` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`naturalTech` tinyint(4) NOT NULL DEFAULT 0,
	`lifeTech` tinyint(4) NOT NULL DEFAULT 3,
	`socialTech` tinyint(4) NOT NULL DEFAULT 5,
	`informaticTech` tinyint(4) NOT NULL DEFAULT 7,
	`naturalToPay` int(11) unsigned NOT NULL,
	`lifeToPay` int(11) unsigned NOT NULL,
	`socialToPay` int(11) unsigned NOT NULL,
	`informaticToPay` int(11) unsigned NOT NULL,

	PRIMARY KEY (`rPlayer`),
	CONSTRAINT fkResearchPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table technology</h2>';

$db->query("DROP TABLE IF EXISTS `technology`");
$db->query("CREATE TABLE IF NOT EXISTS `technology` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(10) unsigned NOT NULL,

	`technology` smallint(5) unsigned NOT NULL,
	`level` tinyint(3) unsigned NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkTechnologyPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table technologyQueue</h2>';

$db->query("DROP TABLE IF EXISTS `technologyQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `technologyQueue` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rPlace` int(11) unsigned NOT NULL,

	`technology` smallint(5) unsigned NOT NULL,
	`targetLevel` tinyint(3) unsigned NOT NULL,

	`dStart` datetime NOT NULL,
	`dEnd` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkTechnologyQueuePlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkTechnologyQueueOB FOREIGN KEY (rPlace) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table recyclingMission</h2>';

$db->query("DROP TABLE IF EXISTS `recyclingMission`");
$db->query("CREATE TABLE IF NOT EXISTS `recyclingMission` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rBase` int(11) unsigned NOT NULL,
	`rTarget` int(11) unsigned NOT NULL,

	`cycleTime` int(11) unsigned NOT NULL,
	`recyclerQuantity` smallint(6) unsigned NOT NULL,

	`uRecycling` datetime NOT NULL,
	`statement` tinyint(3) NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkRecyclingMissionOB FOREIGN KEY (rBase) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table recyclingLog</h2>';

$db->query("DROP TABLE IF EXISTS `recyclingLog`");
$db->query("CREATE TABLE IF NOT EXISTS `recyclingLog` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rRecycling` int(11) unsigned NOT NULL,

	`resources` int(11) unsigned NOT NULL,
	`credits` int(11) unsigned NOT NULL,
	`ship0` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship1` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship2` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship3` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship4` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship5` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship6` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship7` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship8` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship9` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship10` smallint(6) unsigned NOT NULL DEFAULT 0,
	`ship11` smallint(6) unsigned NOT NULL DEFAULT 0,
	`dLog` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkRecyclingLogRec FOREIGN KEY (rRecycling) REFERENCES recyclingMission(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table creditTransaction</h2>';

$db->query("DROP TABLE IF EXISTS `creditTransaction`");
$db->query("CREATE TABLE IF NOT EXISTS `creditTransaction` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(11) unsigned NOT NULL,
	`rColor` int(11) unsigned NOT NULL,

	`amout` int(11) unsigned NOT NULL,
	`dTransaction` datetime NOT NULL,
	`comment` text DEFAULT NULL,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<h2>Ajout de la table colorLink</h2>';
$db->query("DROP TABLE IF EXISTS `colorLink`");
$db->query("CREATE TABLE IF NOT EXISTS `colorLink` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,

	`rColor` tinyint(4) NOT NULL DEFAULT 0,
	`rColorLinked` tinyint(4) NOT NULL DEFAULT 0,
	`statement` tinyint(4) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$values = '';
ASM::$clm->load();
for ($i = 1; $i < ASM::$clm->size(); $i++) {
	for ($j = 1; $j < ASM::$clm->size(); $j++) {
		if (!(($i == ASM::$clm->size() - 1) && ($j == ASM::$clm->size() - 1))) {
			$values .= '(' . $i . ',' . $j . ',' . 0 .'),';
		}
	}
}

$values .= '(' . (ASM::$clm->size() - 1) . ',' . (ASM::$clm->size() - 1) . ',' . 0 .');';

echo '<h3>Remplissage de la table colorLink</h3>';
$qr = $db->prepare("INSERT INTO `colorLink` (`rColor`, `rColorLinked`, `statement`) VALUES" . $values);
$qr->execute();

echo '<h1>Génération de la galaxie</h1>';

GalaxyGenerator::generate();
echo GalaxyGenerator::getLog();
?>