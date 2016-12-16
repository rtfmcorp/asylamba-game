<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationUser;

$availableFactions = $this->getContainer()->getParameter('game.available_factions');
$db = $this->getContainer()->get('database_admin');

$db->query('SET FOREIGN_KEY_CHECKS = 0;');

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table color</h2>';

$db->query("DROP TABLE IF EXISTS `color`");
$db->query("CREATE TABLE IF NOT EXISTS `color` (
	`id` INT unsigned NOT NULL,

	`alive` TINYINT NOT NULL DEFAULT 0,
	`isWinner` TINYINT NOT NULL DEFAULT 0,
	`credits` INT unsigned NOT NULL DEFAULT 0,
	`players` SMALLINT unsigned NOT NULL DEFAULT 0,
	`activePlayers` SMALLINT unsigned NOT NULL DEFAULT 0,
	`rankingPoints` INT unsigned NOT NULL DEFAULT 0,
	`points` INT unsigned NOT NULL DEFAULT 0,
	`sectors` TINYINT unsigned NOT NULL DEFAULT 0,
	`electionStatement` TINYINT NOT NULL DEFAULT 0,
	`isClosed` TINYINT NOT NULL DEFAULT 1,
	`isInGame` TINYINT NOT NULL DEFAULT 0,
	`description` TEXT NULL,

	`dClaimVictory` datetime NULL DEFAULT NULL,
	`dLastElection` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

echo '<h3>Remplissage de la table color</h3>';
$qr = $db->prepare("INSERT INTO `color` (`id`, `alive`, `credits`, `players`, `activePlayers`, `points`, `sectors`, `electionStatement`, `isClosed`, `isInGame`, `description`, `dClaimVictory`, `dLastElection`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, ?)");
$date = Utils::addSecondsToDate(Utils::now(), - 500000);

# génération de la faction zero
$qr->execute(array(0, 0, 0, 0, 0, 0, 0, 1, 1, 0, $date));

# génération des factions disponibles
foreach ($availableFactions as $faction) {
	$qr->execute(array($faction, 1, 0, 0, 0, 0, 0, 1, 0, 1, $date));
}
#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table factionNews</h2>';

$db->query("DROP TABLE IF EXISTS `factionNews`");
$db->query("CREATE TABLE IF NOT EXISTS `factionNews` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rFaction` INT unsigned NOT NULL,

	`title` varchar(255) NOT NULL DEFAULT 'Nouvelle',
	`oContent` text NOT NULL,
	`pContent` text NOT NULL,
	`pinned` TINYINT unsigned NOT NULL DEFAULT 0,
	`statement` TINYINT unsigned NOT NULL DEFAULT 1,

	`dCreation` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkFactionNewsFaction FOREIGN KEY (rFaction) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table player</h2>';

$db->query("DROP TABLE IF EXISTS `player`");
$db->query("CREATE TABLE IF NOT EXISTS `player` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rColor` INT unsigned NOT NULL,
	`rGodfather` INT unsigned NULL,

	`bind` varchar(50) default NULL,
	`name` varchar(25) NOT NULL,
	`avatar` varchar(12) NOT NULL,
	`sex` TINYINT NOT NULL DEFAULT 1,
	`status` SMALLINT unsigned NOT NULL DEFAULT 1,
	`credit` BIGINT unsigned NOT NULL DEFAULT 0,
	`experience` INT unsigned NOT NULL DEFAULT 0,
	`factionPoint` INT unsigned NOT NULL DEFAULT 0,
	`level` TINYINT unsigned DEFAULT NULL DEFAULT 0,
	`victory` INT unsigned DEFAULT NULL DEFAULT 0,
	`defeat` INT unsigned DEFAULT NULL DEFAULT 0,
	`premium` TINYINT NOT NULL DEFAULT 0,
	`statement` TINYINT NOT NULL DEFAULT 0,
	`description` text DEFAULT NULL,

	`stepTutorial` TINYINT unsigned DEFAULT NULL,
	`stepDone` TINYINT unsigned NOT NULL DEFAULT 0,

	`iUniversity` INT unsigned NOT NULL DEFAULT 0,
	`partNaturalSciences` TINYINT unsigned NOT NULL DEFAULT 0,
	`partLifeSciences` TINYINT unsigned NOT NULL DEFAULT 0,
	`partSocialPoliticalSciences` TINYINT unsigned NOT NULL DEFAULT 0,
	`partInformaticEngineering` TINYINT unsigned NOT NULL DEFAULT 0,

	`dInscription` datetime DEFAULT NULL,
	`dLastConnection` datetime DEFAULT NULL,
	`dLastActivity` datetime DEFAULT NULL,
	`uPlayer` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	UNIQUE KEY `name_UNIQUE` (`name`),
	CONSTRAINT fkPlayerColor FOREIGN KEY (rColor) REFERENCES color(id),
	CONSTRAINT fkPlayerPlayer FOREIGN KEY (rGodfather) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT = 1;");

#--------------------------------------------------------------------------------------------
echo '<h3>Ajout du Joueur Gaia</h3>';

$playerManager = $this->getContainer()->get('zeus.player_manager');
$S_PAM_ALL = $playerManager->getCurrentSession();
$playerManager->newSession();

$p = new Player();
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
$p->statement = Player::DEAD;

# Joueur rebelle
$p = clone($p);
$p->bind = Utils::generateString(25);
$p->name = 'Rebelle';
$p->avatar = 'rebel';
$p->rColor = 0;
$playerManager->add($p);

# Jean-Mi
$p = clone($p);
$p->bind = Utils::generateString(25);
$p->name = 'Jean-Mi';
$p->avatar = 'jm';
$p->rColor = 0;
$playerManager->add($p);

# Joueurs de factions
foreach ($availableFactions as $faction) {
	$p = clone($p);
	$p->bind = Utils::generateString(25);
	$p->name = ColorResource::getInfo($faction, 'officialName');
	$p->avatar = ('color-' . $faction);
	$p->rColor = $faction;
	$p->status = 6;

	$playerManager->add($p);
}

$playerManager->changeSession($S_PAM_ALL);

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table sector</h2>';

$db->query("DROP TABLE IF EXISTS `sector`");
$db->query("CREATE TABLE IF NOT EXISTS `sector` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rColor` INT unsigned NOT NULL,
	`rSurrender` INT unsigned DEFAULT NULL,

	`xPosition` SMALLINT unsigned DEFAULT NULL,
	`yPosition` SMALLINT unsigned DEFAULT NULL,
	`xBarycentric` SMALLINT unsigned NOT NULL DEFAULT 0,
	`yBarycentric` SMALLINT unsigned NOT NULL DEFAULT 0,
	`tax` TINYINT unsigned NOT NULL DEFAULT 0,
	`population` INT unsigned NOT NULL,
	`lifePlanet` INT unsigned DEFAULT NULL,
	`points` INT unsigned NOT NULL DEFAULT 1,
	`name` varchar(255) DEFAULT NULL,
	`prime` TINYINT NOT NULL DEFAULT 0,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rSector` INT unsigned NOT NULL,
	`rColor` INT unsigned NOT NULL,

	`xPosition` SMALLINT unsigned DEFAULT NULL,
	`yPosition` SMALLINT unsigned DEFAULT NULL,
	`typeOfSystem` SMALLINT unsigned DEFAULT NULL,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NULL,
	`rSystem` INT unsigned NOT NULL,

	`typeOfPlace` TINYINT unsigned NOT NULL,
	`position` TINYINT unsigned NOT NULL,

	`population` float unsigned NOT NULL,
	`coefResources` float unsigned NOT NULL,
	`coefHistory` float unsigned NOT NULL,
	
	`resources` INT unsigned DEFAULT 0,
	`danger` TINYINT unsigned DEFAULT 0,
	`maxDanger` TINYINT unsigned DEFAULT 0,

	`uPlace` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkPlacePlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlace` INT unsigned NOT NULL,
	`oldPlayer` TINYINT unsigned NOT NULL,
	`newPlayer` TINYINT unsigned NOT NULL,
	`dChangement` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table changeColorSector</h2>';

$db->query("DROP TABLE IF EXISTS `changeColorSector`");
$db->query("CREATE TABLE IF NOT EXISTS `changeColorSector` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rSector` INT unsigned NOT NULL,
	`oldColor` TINYINT unsigned NOT NULL,
	`newColor` TINYINT unsigned NOT NULL,
	`dChangement` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table changeColorSystem</h2>';

$db->query("DROP TABLE IF EXISTS `changeColorSystem`");
$db->query("CREATE TABLE IF NOT EXISTS `changeColorSystem` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rSystem` INT unsigned NOT NULL,
	`oldColor` TINYINT unsigned NOT NULL,
	`newColor` TINYINT unsigned NOT NULL,
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
	`rPlace` INT unsigned NOT NULL,
	`rPlayer` INT unsigned NOT NULL,

	`name` varchar(45) COLLATE utf8_bin NOT NULL,
	`typeOfBase` TINYINT unsigned NOT NULL DEFAULT 0,
	`levelGenerator` TINYINT unsigned DEFAULT 0,
	`levelRefinery` TINYINT unsigned DEFAULT 0,
	`levelDock1` TINYINT unsigned DEFAULT 0,
	`levelDock2` TINYINT unsigned DEFAULT 0,
	`levelDock3` TINYINT unsigned DEFAULT 0,
	`levelTechnosphere` TINYINT unsigned DEFAULT 0,
	`levelCommercialPlateforme` TINYINT unsigned DEFAULT 0,
	`levelStorage` TINYINT unsigned DEFAULT 0,
	`levelRecycling` TINYINT unsigned DEFAULT 0,
	`levelSpatioport` TINYINT unsigned DEFAULT 0,
	`points` INT unsigned DEFAULT 0,

	`iSchool` INT unsigned DEFAULT 0,
	`iAntiSpy` INT unsigned DEFAULT 0,
	`antiSpyAverage` INT unsigned DEFAULT 0,

	`pegaseStorage` SMALLINT unsigned DEFAULT 0,
	`satyreStorage` SMALLINT unsigned DEFAULT 0,
	`sireneStorage` SMALLINT unsigned DEFAULT 0,
	`dryadeStorage` SMALLINT unsigned DEFAULT 0,
	`chimereStorage` SMALLINT unsigned DEFAULT 0,
	`meduseStorage` SMALLINT unsigned DEFAULT 0,
	`griffonStorage` SMALLINT unsigned DEFAULT 0,
	`cyclopeStorage` SMALLINT unsigned DEFAULT 0,
	`minotaureStorage` SMALLINT unsigned DEFAULT 0,
	`hydreStorage` SMALLINT unsigned DEFAULT 0,
	`cerbereStorage` SMALLINT unsigned DEFAULT 0,
	`phenixStorage` SMALLINT unsigned DEFAULT 0,

	`motherShip` TINYINT DEFAULT 0,
	`resourcesStorage` INT unsigned DEFAULT 0,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rOrbitalBase` INT unsigned NOT NULL,
	`rOrbitalBaseLinked` INT unsigned NOT NULL,

	`imageLink` varchar(10) NOT NULL,
	`distance` SMALLINT unsigned NOT NULL,
	`price` INT unsigned NOT NULL,
	`income` INT unsigned NOT NULL,

	`dProposition` datetime DEFAULT NULL,
	`dCreation` datetime DEFAULT NULL,
	`statement` TINYINT unsigned NOT NULL COMMENT '0 = pas acceptée, 1 = active',

	PRIMARY KEY (`id`),
	CONSTRAINT fkCommercialRouteOrbitalBaseA FOREIGN KEY (rOrbitalBase) REFERENCES orbitalBase(rPlace),
	CONSTRAINT fkCommercialRouteOrbitalBaseB FOREIGN KEY (rOrbitalBaseLinked) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table orbitalBaseBuildingQueue</h2>';

$db->query("DROP TABLE IF EXISTS `orbitalBaseBuildingQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `orbitalBaseBuildingQueue` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rOrbitalBase` INT unsigned NOT NULL,

	`buildingNumber` TINYINT unsigned NOT NULL,
	`targetLevel` TINYINT unsigned NOT NULL,
	`dStart` datetime NOT NULL,
	`dEnd` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkOrbitalBaseBuildingQueueOrbitalBase FOREIGN KEY (rOrbitalBase) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table orbitalBaseShipQueue</h2>';

$db->query("DROP TABLE IF EXISTS `orbitalBaseShipQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `orbitalBaseShipQueue` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rOrbitalBase` INT unsigned NOT NULL,

	`dockType` TINYINT unsigned NOT NULL,
	`shipNumber` TINYINT unsigned NOT NULL,
	`quantity` SMALLINT unsigned NOT NULL,

	`dStart` datetime NOT NULL,
	`dEnd` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkOrbitalBaseShipQueueOrbitalBase FOREIGN KEY (rOrbitalBase) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table transaction</h2>';

$db->query("DROP TABLE IF EXISTS `transaction`");
$db->query("CREATE TABLE IF NOT EXISTS `transaction` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`rPlace` INT unsigned NOT NULL,

	`type` TINYINT NOT NULL COMMENT '0 = resource, 1 = ship, 2 = commander',
	`quantity` INT NOT NULL,
	`identifier` SMALLINT DEFAULT NULL,
	`price` INT NOT NULL,
	`commercialShipQuantity` SMALLINT NOT NULL,
	`statement` TINYINT NOT NULL COMMENT '0 = proposed, 1 = completed, 2 = canceled',

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`rBase` INT unsigned NOT NULL,
	`rBaseDestination` INT unsigned NOT NULL,
	`rTransaction` INT unsigned DEFAULT NULL,

	`resourceTransported` INT DEFAULT NULL,
	`shipQuantity` INT NOT NULL,

	`dDeparture` datetime,
	`dArrival` datetime,
	`statement` SMALLINT NOT NULL COMMENT '0 = prêt au départ, 1 = aller, 2 = retour',

	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commercialTax</h2>';

$db->query("DROP TABLE IF EXISTS `commercialTax`");
$db->query("CREATE TABLE IF NOT EXISTS `commercialTax` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`faction` SMALLINT NOT NULL,
	`relatedFaction` SMALLINT NOT NULL,
	`exportTax` float NOT NULL,
	`importTax` float NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<h3>Remplissage de la table commercialTax</h3>';
$qr = $db->prepare("INSERT INTO `commercialTax` (`faction`, `relatedFaction`, `exportTax`, `importTax`) VALUES (?, ?, 5, 5)");

# génération des taxes
foreach ($availableFactions as $faction) {
	foreach ($availableFactions as $rfaction) {
		$qr->execute(array($faction, $rfaction));
	}
}

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table law</h2>';

$db->query("DROP TABLE IF EXISTS `law`");
$db->query("CREATE TABLE IF NOT EXISTS `law` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rColor` INT unsigned NOT NULL,

	`type` INT NOT NULL,
	`statement` INT NOT NULL,
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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,

	`rLaw` INT unsigned NOT NULL,
	`rPlayer` INT unsigned NOT NULL,

	`vote` TINYINT NOT NULL,
	`dVotation` date NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkVoteLawLaw FOREIGN KEY (rLaw) REFERENCES law(id),
	CONSTRAINT fkVoteLawPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table forumTopic</h2>';

$db->query("DROP TABLE IF EXISTS `forumTopic`");
$db->query("CREATE TABLE IF NOT EXISTS `forumTopic` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rColor` INT unsigned NOT NULL,
	`rPlayer` INT unsigned NOT NULL,
	`rForum` INT unsigned NOT NULL,

	`title` varchar(255) NOT NULL,
	`isClosed` TINYINT NOT NULL DEFAULT 0,
	`isArchived` TINYINT NOT NULL DEFAULT 0,
	`isUp` TINYINT NOT NULL DEFAULT 0,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`rTopic` INT unsigned NOT NULL,

	`oContent` text NOT NULL,
	`pContent` text NOT NULL,

	`statement` INT NOT NULL DEFAULT 0,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`rTopic` INT unsigned NOT NULL,

	`dView` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkForumLastViewsPlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkForumLastViewsTopic FOREIGN KEY (rTopic) REFERENCES forumTopic(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table election</h2>';

$db->query("DROP TABLE IF EXISTS `election`");
$db->query("CREATE TABLE IF NOT EXISTS `election` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rColor` INT unsigned NOT NULL,

	`dElection` datetime NOT NULL,
	PRIMARY KEY (`id`),
	CONSTRAINT fkElectionColor FOREIGN KEY (rColor) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table candidate</h2>';

$db->query("DROP TABLE IF EXISTS `candidate`");
$db->query("CREATE TABLE IF NOT EXISTS `candidate` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rElection` INT unsigned NOT NULL,
	`rPlayer` INT unsigned NOT NULL,

	`chiefChoice` TINYINT unsigned DEFAULT 1,
	`treasurerChoice` TINYINT unsigned DEFAULT 1,
	`warlordChoice` TINYINT unsigned DEFAULT 1,
	`ministerChoice` TINYINT unsigned DEFAULT 1,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rCandidate` INT unsigned NOT NULL,
	`rPlayer` INT unsigned NOT NULL,
	`rElection` INT unsigned NOT NULL,

	`dVotation` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkVoteCandidate FOREIGN KEY (rCandidate) REFERENCES player(id),
	CONSTRAINT fkVotePlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkVoteElection FOREIGN KEY (rElection) REFERENCES election(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commander</h2>';

$db->query("DROP TABLE IF EXISTS `commander`");
$db->query("CREATE TABLE IF NOT EXISTS `commander` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`rBase` INT unsigned NOT NULL,

	`name` varchar(45) NOT NULL,
	`comment` text,
	`sexe` TINYINT NOT NULL DEFAULT 1,
	`age` INT unsigned NOT NULL DEFAULT 20,
	`avatar` varchar(20) NOT NULL,
	`level` TINYINT unsigned NOT NULL DEFAULT 1,
	`experience` INT unsigned NOT NULL DEFAULT 1,
	`palmares` INT unsigned NOT NULL DEFAULT 0,
	`statement` TINYINT unsigned NOT NULL DEFAULT 0,
	`line` TINYINT unsigned DEFAULT NULL,
	`rStartPlace` INT unsigned DEFAULT NULL,
	`rDestinationPlace` INT unsigned DEFAULT NULL,
	`dStart` datetime DEFAULT NULL,
	`dArrival` datetime DEFAULT NULL,
	`resources` INT unsigned NOT NULL DEFAULT 0,
	`travelType` TINYINT unsigned DEFAULT NULL,
	`travelLength` TINYINT unsigned DEFAULT NULL,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rCommander` INT unsigned NOT NULL,

	`ship0` SMALLINT unsigned DEFAULT 0,
	`ship1` SMALLINT unsigned DEFAULT 0,
	`ship2` SMALLINT unsigned DEFAULT 0,
	`ship3` SMALLINT unsigned DEFAULT 0,
	`ship4` SMALLINT unsigned DEFAULT 0,
	`ship5` SMALLINT unsigned DEFAULT 0,
	`ship6` SMALLINT unsigned DEFAULT 0,
	`ship7` SMALLINT unsigned DEFAULT 0,
	`ship8` SMALLINT unsigned DEFAULT 0,
	`ship9` SMALLINT unsigned DEFAULT 0,
	`ship10` SMALLINT unsigned DEFAULT 0,
	`ship11` SMALLINT unsigned DEFAULT 0,

	`dCreation` datetime DEFAULT NULL,
	`dLastModification` datetime DEFAULT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkSquadronCommander FOREIGN KEY (rCommander) REFERENCES commander(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table report</h2>';

$db->query("DROP TABLE IF EXISTS `report`");
$db->query("CREATE TABLE IF NOT EXISTS `report` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayerAttacker` INT unsigned NOT NULL,
	`rPlayerDefender` INT unsigned NOT NULL,
	`rPlayerWinner` INT unsigned NOT NULL,

	`resources` INT unsigned NOT NULL,
	`expCom` INT unsigned NOT NULL,
	`rPlace` INT unsigned NOT NULL,
	`placeName` varchar(45) NOT NULL,
	`type` TINYINT unsigned DEFAULT NULL,
	`isLegal` TINYINT unsigned DEFAULT 1,
	`hasBeenPunished` TINYINT unsigned DEFAULT 0,
	`round` INT unsigned DEFAULT 0,
	`importance` INT unsigned DEFAULT NULL,
	`pevInBeginA` SMALLINT unsigned,
	`pevInBeginD` SMALLINT unsigned,
	`pevAtEndA` SMALLINT unsigned,
	`pevAtEndD` SMALLINT unsigned,

	`avatarA` varchar(255) NOT NULL,
	`avatarD` varchar(255) NOT NULL,
	`nameA` varchar(255) NOT NULL,
	`nameD` varchar(255) NOT NULL,
	`levelA` SMALLINT unsigned NOT NULL,
	`levelD` SMALLINT unsigned NOT NULL,
	`experienceA` INT unsigned NOT NULL,
	`experienceD` INT unsigned NOT NULL,
	`palmaresA` SMALLINT unsigned NOT NULL,
	`palmaresD` SMALLINT unsigned NOT NULL,
	`expPlayerA` INT unsigned NOT NULL,
	`expPlayerD` INT unsigned NOT NULL,
	`statementAttacker` TINYINT unsigned NOT NULL DEFAULT 0,
	`statementDefender` TINYINT unsigned NOT NULL DEFAULT 0,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rReport` INT unsigned NOT NULL,
	`rCommander` INT unsigned NULL,

	`position` TINYINT unsigned NOT NULL,
	`round` INT NOT NULL,

	`ship0` SMALLINT unsigned NOT NULL,
	`ship1` SMALLINT unsigned NOT NULL,
	`ship2` SMALLINT unsigned NOT NULL,
	`ship3` SMALLINT unsigned NOT NULL,
	`ship4` SMALLINT unsigned NOT NULL,
	`ship5` SMALLINT unsigned NOT NULL,
	`ship6` SMALLINT unsigned NOT NULL,
	`ship7` SMALLINT unsigned NOT NULL,
	`ship8` SMALLINT unsigned NOT NULL,
	`ship9` SMALLINT unsigned NOT NULL,
	`ship10` SMALLINT unsigned NOT NULL,
	`ship11` SMALLINT unsigned NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkSquadronReportReport FOREIGN KEY (rReport) REFERENCES report(id),
	CONSTRAINT fkSquadronReportCommander FOREIGN KEY (rCommander) REFERENCES commander(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table spyReport</h2>';

$db->query("DROP TABLE IF EXISTS `spyReport`");
$db->query("CREATE TABLE IF NOT EXISTS `spyReport` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`rPlace` INT unsigned NOT NULL,
	
	`price` INT unsigned NOT NULL,
	`placeColor` SMALLINT unsigned NOT NULL,
	`typeOfBase` TINYINT unsigned NOT NULL,
	`typeOfOrbitalBase` TINYINT unsigned NOT NULL,
	`placeName` varchar(255) NOT NULL,
	`points` INT unsigned NOT NULL,
	`rEnemy` INT unsigned NOT NULL,
	`enemyName` varchar(255) NOT NULL,
	`enemyAvatar` varchar(255) NOT NULL,
	`enemyLevel` INT unsigned NOT NULL,
	`resources` INT unsigned NOT NULL,
	`shipsInStorage` text NOT NULL,
	`antiSpyInvest` INT NOT NULL,
	`commercialRouteIncome` INT NOT NULL,
	`commanders` text NOT NULL,
	`success` SMALLINT unsigned NOT NULL,
	`type` TINYINT unsigned NOT NULL,

	`dSpying` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkSpyReportPlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkSpyReportPlace FOREIGN KEY (rPlace) REFERENCES place(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table ranking</h2>';

$qr = $db->prepare("DROP TABLE IF EXISTS `ranking`");
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `ranking` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`dRanking` datetime NOT NULL,
	`player` TINYINT unsigned NOT NULL DEFAULT 0,
	`faction` TINYINT unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table playerRanking</h2>';

$qr = $db->prepare("DROP TABLE IF EXISTS `playerRanking`");
$qr->execute();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `playerRanking` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rRanking` INT unsigned NOT NULL,
	`rPlayer` INT unsigned NOT NULL,

	`general` INT unsigned NOT NULL,
	`generalPosition` SMALLINT NOT NULL,
	`generalVariation` SMALLINT NOT NULL,

	`experience` INT unsigned NOT NULL,
	`experiencePosition` SMALLINT NOT NULL,
	`experienceVariation` SMALLINT NOT NULL,

	`butcher` INT unsigned NOT NULL,
	`butcherDestroyedPEV` INT unsigned NOT NULL,
	`butcherLostPEV` INT unsigned NOT NULL,
	`butcherPosition` SMALLINT NOT NULL,
	`butcherVariation` SMALLINT NOT NULL,

	`armies` INT unsigned NOT NULL,
	`armiesPosition` SMALLINT NOT NULL,
	`armiesVariation` SMALLINT NOT NULL,

	`trader` INT unsigned NOT NULL,
	`traderPosition` SMALLINT NOT NULL,
	`traderVariation` SMALLINT NOT NULL,

	`resources` INT unsigned NOT NULL,
	`resourcesPosition` SMALLINT NOT NULL,
	`resourcesVariation` SMALLINT NOT NULL,

	`fight` INT unsigned NOT NULL,
	`victories` INT unsigned NOT NULL,
	`defeat` INT unsigned NOT NULL,
	`fightPosition` SMALLINT NOT NULL,
	`fightVariation` SMALLINT NOT NULL,

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
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rRanking` INT unsigned NOT NULL,
	`rFaction` INT unsigned NOT NULL,

	`points` INT unsigned NOT NULL,
	`pointsPosition` SMALLINT NOT NULL,
	`pointsVariation` SMALLINT NOT NULL,
	`newPoints` SMALLINT NOT NULL,

	`general` INT unsigned NOT NULL,
	`generalPosition` SMALLINT NOT NULL,
	`generalVariation` SMALLINT NOT NULL,

	`wealth` INT unsigned NOT NULL,
	`wealthPosition` SMALLINT NOT NULL,
	`wealthVariation` SMALLINT NOT NULL,

	`territorial` INT unsigned NOT NULL,
	`territorialPosition` SMALLINT NOT NULL,
	`territorialVariation` SMALLINT NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkFactionRankingRanking FOREIGN KEY (rRanking) REFERENCES ranking(id),
	CONSTRAINT fkFactionRankingPlayer FOREIGN KEY (rFaction) REFERENCES color(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

#--------------------------------------------------------------------------------------------
echo '<h1>Ajout du module de Conversation</h1>';

echo '<h2>Ajout de la table Conversation</h2>';

$db = $this->getContainer()->get('database');
$db->query("DROP TABLE IF EXISTS `conversation`");
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `conversation` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NULL,
	`messages` INT(5) NOT NULL DEFAULT 0,
	`type` TINYINT(2) NOT NULL DEFAULT 1,
	`dCreation` datetime NOT NULL,
	`dLastMessage` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

echo '<h2>Ajout de la table userConversation</h2>';

$db->query("DROP TABLE IF EXISTS `conversationUser`");
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `conversationUser` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`rConversation` INT(11) NOT NULL,
	`rPlayer` INT(11) NOT NULL,
	`playerStatement` INT(5) NOT NULL DEFAULT 0,
	`convStatement` INT(5) NOT NULL DEFAULT 0,
	`dLastView` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

echo '<h2>Ajout de la table messageConversation</h2>';

$db->query("DROP TABLE IF EXISTS `conversationMessage`");
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `conversationMessage` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`rConversation` INT(11) NOT NULL,
	`rPlayer` INT(11) NOT NULL,
	`type` INT(5) NOT NULL DEFAULT 0,

	`content` TEXT NOT NULL,

	`dCreation` datetime NOT NULL,
	`dLastModification` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

echo '<h2>Remplissage des conversations</h2>';

# conv jeanmi
$conv = new Conversation();
$conv->messages = 0;
$conv->type = Conversation::TY_SYSTEM;
$conv->title = 'Jean-Mi, administrateur système';
$conv->dCreation = Utils::now();
$conv->dLastMessage = Utils::now();
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$conversationManager->add($conv);

$user = new ConversationUser();
$user->rConversation = $conv->id;
$user->rPlayer = ID_JEANMI;
$user->convPlayerStatement = ConversationUser::US_ADMIN;
$user->convStatement = ConversationUser::CS_DISPLAY;
$user->dLastView = Utils::now();
$conversationUserManager = $this->getContainer()->get('hermes.conversation_user_manager');
$conversationUserManager->add($user);

$S_PAM_ALL = $playerManager->getCurrentSession();
$playerManager->newSession(FALSE);
$playerManager->load(
	['rColor' => $availableFactions, 'statement' => Player::DEAD]
);

for ($i = 0; $i < $playerManager->size(); $i++) {
	$player = $playerManager->get($i);

	$conv = new Conversation();
	$conv->messages = 0;
	$conv->type = Conversation::TY_SYSTEM;
	$conv->title = 'Communication de ' . ColorResource::getInfo($player->rColor, 'popularName');
	$conv->dCreation = Utils::now();
	$conv->dLastMessage = Utils::now();
	$conversationManager->add($conv);

	$user = new ConversationUser();
	$user->rConversation = $conv->id;
	$user->rPlayer = $player->id;
	$user->convPlayerStatement = ConversationUser::US_ADMIN;
	$user->convStatement = ConversationUser::CS_DISPLAY;
	$user->dLastView = Utils::now();
	$conversationUserManager->add($user);
}

$playerManager->changeSession($S_PAM_ALL);

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table notification</h2>';

$db->query("DROP TABLE IF EXISTS `notification`");
$db->query("CREATE TABLE IF NOT EXISTS `notification` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`title` varchar(100) NOT NULL,
	`content` text,
	`dSending` datetime NOT NULL,
	`readed` TINYINT DEFAULT 0,
	`archived` TINYINT DEFAULT 0,

	PRIMARY KEY (`id`),
	CONSTRAINT fkNotificationPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table roadMap</h2>';

$db->query("DROP TABLE IF EXISTS `roadMap`");
$db->query("CREATE TABLE IF NOT EXISTS `roadMap` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`rPlayer` INT NOT NULL,
	`oContent` text NOT NULL,
	`pContent` text NOT NULL,
	`statement` TINYINT NOT NULL COMMENT '0 = caché, 1 = affiché',
	`dCreation` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table research</h2>';

$db->query("DROP TABLE IF EXISTS `research`");
$db->query("CREATE TABLE IF NOT EXISTS `research` (
	`rPlayer` INT unsigned NOT NULL,

	`mathLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`physLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`chemLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`bioLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`mediLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`econoLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`psychoLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`networkLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`algoLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`statLevel` TINYINT unsigned NOT NULL DEFAULT 0,
	`naturalTech` TINYINT NOT NULL DEFAULT 0,
	`lifeTech` TINYINT NOT NULL DEFAULT 3,
	`socialTech` TINYINT NOT NULL DEFAULT 5,
	`informaticTech` TINYINT NOT NULL DEFAULT 7,
	`naturalToPay` INT unsigned NOT NULL,
	`lifeToPay` INT unsigned NOT NULL,
	`socialToPay` INT unsigned NOT NULL,
	`informaticToPay` INT unsigned NOT NULL,

	PRIMARY KEY (`rPlayer`),
	CONSTRAINT fkResearchPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table technology</h2>';

$db->query("DROP TABLE IF EXISTS `technology`");
$db->query("CREATE TABLE IF NOT EXISTS `technology` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,

	`technology` SMALLINT unsigned NOT NULL,
	`level` TINYINT unsigned NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkTechnologyPlayer FOREIGN KEY (rPlayer) REFERENCES player(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table technologyQueue</h2>';

$db->query("DROP TABLE IF EXISTS `technologyQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `technologyQueue` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`rPlace` INT unsigned NOT NULL,

	`technology` SMALLINT unsigned NOT NULL,
	`targetLevel` TINYINT unsigned NOT NULL,

	`dStart` datetime NOT NULL,
	`dEnd` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkTechnologyQueuePlayer FOREIGN KEY (rPlayer) REFERENCES player(id),
	CONSTRAINT fkTechnologyQueueOB FOREIGN KEY (rPlace) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
$db->query("DROP TABLE IF EXISTS `recyclingLog`");
$db->query("DROP TABLE IF EXISTS `recyclingMission`");

echo '<h2>Ajout de la table recyclingMission</h2>';

$db->query("CREATE TABLE IF NOT EXISTS `recyclingMission` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rBase` INT unsigned NOT NULL,
	`rTarget` INT unsigned NOT NULL,

	`cycleTime` INT unsigned NOT NULL,
	`recyclerQuantity` SMALLINT unsigned NOT NULL,
	`addToNextMission` SMALLINT unsigned NOT NULL,

	`uRecycling` datetime NOT NULL,
	`statement` TINYINT NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkRecyclingMissionOB FOREIGN KEY (rBase) REFERENCES orbitalBase(rPlace)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table recyclingLog</h2>';

$db->query("CREATE TABLE IF NOT EXISTS `recyclingLog` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rRecycling` INT unsigned NOT NULL,

	`resources` INT unsigned NOT NULL,
	`credits` INT unsigned NOT NULL,
	`ship0` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship1` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship2` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship3` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship4` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship5` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship6` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship7` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship8` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship9` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship10` SMALLINT unsigned NOT NULL DEFAULT 0,
	`ship11` SMALLINT unsigned NOT NULL DEFAULT 0,
	`dLog` datetime NOT NULL,

	PRIMARY KEY (`id`),
	CONSTRAINT fkRecyclingLogRec FOREIGN KEY (rRecycling) REFERENCES recyclingMission(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table creditTransaction</h2>';

$db->query("DROP TABLE IF EXISTS `creditTransaction`");
$db->query("CREATE TABLE IF NOT EXISTS `creditTransaction` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rSender` INT unsigned NOT NULL,
	`rReceiver` INT unsigned NOT NULL,
	`amount` INT unsigned NOT NULL,
	`type` TINYINT unsigned NOT NULL,
	`dTransaction` datetime NOT NULL,
	`comment` text DEFAULT NULL,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<h2>Ajout de la table colorLink</h2>';
$db->query("DROP TABLE IF EXISTS `colorLink`");
$db->query("CREATE TABLE IF NOT EXISTS `colorLink` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,

	`rColor` TINYINT NOT NULL DEFAULT 0,
	`rColorLinked` TINYINT NOT NULL DEFAULT 0,
	`statement` TINYINT unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$values = '';
$colorManager = $this->getContainer()->get('demeter.color_manager');
$colorManager->load();
for ($i = 1; $i < $colorManager->size(); $i++) {
	for ($j = 1; $j < $colorManager->size(); $j++) {
		if (!(($i == $colorManager->size() - 1) && ($j == $colorManager->size() - 1))) {
			$values .= '(' . $colorManager->get($i)->id . ',' . $colorManager->get($j)->id . ',' . 0 .'),';
		}
	}
}

$values .= '(' . $colorManager->get($colorManager->size() - 1)->id . ',' . $colorManager->get($colorManager->size() - 1)->id . ',' . 0 .');';

echo '<h3>Remplissage de la table colorLink</h3>';
$qr = $db->prepare("INSERT INTO `colorLink` (`rColor`, `rColorLinked`, `statement`) VALUES" . $values);
$qr->execute();

$db->query('SET FOREIGN_KEY_CHECKS = 1;');

if (DATA_ANALYSIS) {
	echo '<h1>Création des tables du module d\'analyse</h1>';

	include 'data-analysis/player.php';
	include 'data-analysis/playerDaily.php';
//	include 'data-analysis/fleetMovement.php';
	include 'data-analysis/commercialRelation.php';
	include 'data-analysis/socialRelation.php';
	include 'data-analysis/baseAction.php';
}

echo '<h1>Génération de la galaxie</h1>';

$galaxyGenerator = $this->getContainer()->get('gaia.galaxy_generator');
$galaxyGenerator->generate();
echo $galaxyGenerator->getLog();
