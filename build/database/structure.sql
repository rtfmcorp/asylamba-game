START TRANSACTION;

CREATE TABLE `budget__donations` (
  `id` int(10) UNSIGNED NOT NULL,
  `player_bind_key` varchar(50) NOT NULL,
  `token` varchar(30) NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `candidate` (
  `id` int(10) UNSIGNED NOT NULL,
  `rElection` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `chiefChoice` tinyint(3) UNSIGNED DEFAULT '1',
  `treasurerChoice` tinyint(3) UNSIGNED DEFAULT '1',
  `warlordChoice` tinyint(3) UNSIGNED DEFAULT '1',
  `ministerChoice` tinyint(3) UNSIGNED DEFAULT '1',
  `program` text,
  `dPresentation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `changeColorPlace` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlace` int(10) UNSIGNED NOT NULL,
  `oldPlayer` tinyint(3) UNSIGNED NOT NULL,
  `newPlayer` tinyint(3) UNSIGNED NOT NULL,
  `dChangement` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `changeColorSector` (
  `id` int(10) UNSIGNED NOT NULL,
  `rSector` int(10) UNSIGNED NOT NULL,
  `oldColor` tinyint(3) UNSIGNED NOT NULL,
  `newColor` tinyint(3) UNSIGNED NOT NULL,
  `dChangement` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `changeColorSystem` (
  `id` int(10) UNSIGNED NOT NULL,
  `rSystem` int(10) UNSIGNED NOT NULL,
  `oldColor` tinyint(3) UNSIGNED NOT NULL,
  `newColor` tinyint(3) UNSIGNED NOT NULL,
  `dChangement` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `color` (
  `id` int(10) UNSIGNED NOT NULL,
  `alive` tinyint(4) NOT NULL DEFAULT '0',
  `isWinner` tinyint(4) NOT NULL DEFAULT '0',
  `credits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `players` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `activePlayers` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `rankingPoints` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `points` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sectors` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `regime` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `electionStatement` tinyint(4) NOT NULL DEFAULT '0',
  `isClosed` tinyint(4) NOT NULL DEFAULT '1',
  `isInGame` tinyint(4) NOT NULL DEFAULT '0',
  `description` text,
  `dClaimVictory` datetime DEFAULT NULL,
  `dLastElection` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `colorLink` (
  `id` int(10) UNSIGNED NOT NULL,
  `rColor` tinyint(4) NOT NULL DEFAULT '0',
  `rColorLinked` tinyint(4) NOT NULL DEFAULT '0',
  `statement` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `commander` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rBase` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `comment` text,
  `sexe` tinyint(4) NOT NULL DEFAULT '1',
  `age` int(10) UNSIGNED NOT NULL DEFAULT '20',
  `avatar` varchar(20) NOT NULL,
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `experience` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `palmares` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `statement` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `line` tinyint(3) UNSIGNED DEFAULT NULL,
  `rStartPlace` int(10) UNSIGNED DEFAULT NULL,
  `rDestinationPlace` int(10) UNSIGNED DEFAULT NULL,
  `dStart` datetime DEFAULT NULL,
  `dArrival` datetime DEFAULT NULL,
  `resources` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `travelType` tinyint(3) UNSIGNED DEFAULT NULL,
  `travelLength` tinyint(3) UNSIGNED DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL,
  `dAffectation` datetime DEFAULT NULL,
  `dDeath` datetime DEFAULT NULL,
  `uCommander` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `commercialRoute` (
  `id` int(10) UNSIGNED NOT NULL,
  `rOrbitalBase` int(10) UNSIGNED NOT NULL,
  `rOrbitalBaseLinked` int(10) UNSIGNED NOT NULL,
  `imageLink` varchar(10) NOT NULL,
  `distance` smallint(5) UNSIGNED NOT NULL,
  `price` int(10) UNSIGNED NOT NULL,
  `income` int(10) UNSIGNED NOT NULL,
  `dProposition` datetime DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL,
  `statement` tinyint(3) UNSIGNED NOT NULL COMMENT '0 = pas acceptée, 1 = active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `commercialShipping` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rBase` int(10) UNSIGNED NOT NULL,
  `rBaseDestination` int(10) UNSIGNED NOT NULL,
  `rTransaction` int(10) UNSIGNED DEFAULT NULL,
  `resourceTransported` int(11) DEFAULT NULL,
  `shipQuantity` int(11) NOT NULL,
  `dDeparture` datetime DEFAULT NULL,
  `dArrival` datetime DEFAULT NULL,
  `statement` smallint(6) NOT NULL COMMENT '0 = prêt au départ, 1 = aller, 2 = retour'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `commercialTax` (
  `id` int(11) NOT NULL,
  `faction` smallint(6) NOT NULL,
  `relatedFaction` smallint(6) NOT NULL,
  `exportTax` float NOT NULL,
  `importTax` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conversation` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `messages` int(5) NOT NULL DEFAULT '0',
  `type` tinyint(2) NOT NULL DEFAULT '1',
  `dCreation` datetime NOT NULL,
  `dLastMessage` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conversationMessage` (
  `id` int(11) NOT NULL,
  `rConversation` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `type` int(5) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `dCreation` datetime NOT NULL,
  `dLastModification` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conversationUser` (
  `id` int(11) NOT NULL,
  `rConversation` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `playerStatement` int(5) NOT NULL DEFAULT '0',
  `convStatement` int(5) NOT NULL DEFAULT '0',
  `dLastView` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `creditTransaction` (
  `id` int(10) UNSIGNED NOT NULL,
  `rSender` int(10) UNSIGNED NOT NULL,
  `rReceiver` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `dTransaction` datetime NOT NULL,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `election` (
  `id` int(10) UNSIGNED NOT NULL,
  `rColor` int(10) UNSIGNED NOT NULL,
  `dElection` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `factionNews` (
  `id` int(10) UNSIGNED NOT NULL,
  `rFaction` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'Nouvelle',
  `oContent` text NOT NULL,
  `pContent` text NOT NULL,
  `pinned` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `statement` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `dCreation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `factionRanking` (
  `id` int(10) UNSIGNED NOT NULL,
  `rRanking` int(10) UNSIGNED NOT NULL,
  `rFaction` int(10) UNSIGNED NOT NULL,
  `points` int(10) UNSIGNED NOT NULL,
  `pointsPosition` smallint(6) NOT NULL,
  `pointsVariation` smallint(6) NOT NULL,
  `newPoints` smallint(6) NOT NULL,
  `general` int(10) UNSIGNED NOT NULL,
  `generalPosition` smallint(6) NOT NULL,
  `generalVariation` smallint(6) NOT NULL,
  `wealth` int(10) UNSIGNED NOT NULL,
  `wealthPosition` smallint(6) NOT NULL,
  `wealthVariation` smallint(6) NOT NULL,
  `territorial` int(10) UNSIGNED NOT NULL,
  `territorialPosition` smallint(6) NOT NULL,
  `territorialVariation` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `forumLastView` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rTopic` int(10) UNSIGNED NOT NULL,
  `dView` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `forumMessage` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rTopic` int(10) UNSIGNED NOT NULL,
  `oContent` text NOT NULL,
  `pContent` text NOT NULL,
  `statement` int(11) NOT NULL DEFAULT '0',
  `dCreation` datetime NOT NULL,
  `dLastModification` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `forumTopic` (
  `id` int(10) UNSIGNED NOT NULL,
  `rColor` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rForum` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `isClosed` tinyint(4) NOT NULL DEFAULT '0',
  `isArchived` tinyint(4) NOT NULL DEFAULT '0',
  `isUp` tinyint(4) NOT NULL DEFAULT '0',
  `dCreation` datetime NOT NULL,
  `dLastMessage` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `law` (
  `id` int(10) UNSIGNED NOT NULL,
  `rColor` int(10) UNSIGNED NOT NULL,
  `type` int(11) NOT NULL,
  `statement` int(11) NOT NULL,
  `options` text,
  `dEnd` datetime DEFAULT NULL,
  `dEndVotation` datetime DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `news` (
   `id` int(10) UNSIGNED NOT NULL,
   `title` varchar(255) NOT NULL,
   `content` text,
   `type` varchar(15) NOT NULL,
   `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `news__military` (
   `news_id` int(10) UNSIGNED NOT NULL,
   `attacker_id` int(10) UNSIGNED NOT NULL,
   `defender_id` int(10) UNSIGNED NOT NULL,
   `place_id`  int(10) UNSIGNED NOT NULL,
   `type` varchar(15) NOT NULL,
   `is_victory` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `news__politics` (
    `news_id` int(10) UNSIGNED NOT NULL,
    `faction_id` int(10) UNSIGNED NOT NULL,
    `type` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `news__trade` (
    `news_id` int(10) UNSIGNED NOT NULL,
    `transaction_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `notification` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text,
  `dSending` datetime NOT NULL,
  `readed` tinyint(4) DEFAULT '0',
  `archived` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `orbitalBase` (
  `rPlace` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `typeOfBase` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `levelGenerator` tinyint(3) UNSIGNED DEFAULT '0',
  `levelRefinery` tinyint(3) UNSIGNED DEFAULT '0',
  `levelDock1` tinyint(3) UNSIGNED DEFAULT '0',
  `levelDock2` tinyint(3) UNSIGNED DEFAULT '0',
  `levelDock3` tinyint(3) UNSIGNED DEFAULT '0',
  `levelTechnosphere` tinyint(3) UNSIGNED DEFAULT '0',
  `levelCommercialPlateforme` tinyint(3) UNSIGNED DEFAULT '0',
  `levelStorage` tinyint(3) UNSIGNED DEFAULT '0',
  `levelRecycling` tinyint(3) UNSIGNED DEFAULT '0',
  `levelSpatioport` tinyint(3) UNSIGNED DEFAULT '0',
  `points` int(10) UNSIGNED DEFAULT '0',
  `iSchool` int(10) UNSIGNED DEFAULT '0',
  `iAntiSpy` int(10) UNSIGNED DEFAULT '0',
  `antiSpyAverage` int(10) UNSIGNED DEFAULT '0',
  `pegaseStorage` smallint(5) UNSIGNED DEFAULT '0',
  `satyreStorage` smallint(5) UNSIGNED DEFAULT '0',
  `sireneStorage` smallint(5) UNSIGNED DEFAULT '0',
  `dryadeStorage` smallint(5) UNSIGNED DEFAULT '0',
  `chimereStorage` smallint(5) UNSIGNED DEFAULT '0',
  `meduseStorage` smallint(5) UNSIGNED DEFAULT '0',
  `griffonStorage` smallint(5) UNSIGNED DEFAULT '0',
  `cyclopeStorage` smallint(5) UNSIGNED DEFAULT '0',
  `minotaureStorage` smallint(5) UNSIGNED DEFAULT '0',
  `hydreStorage` smallint(5) UNSIGNED DEFAULT '0',
  `cerbereStorage` smallint(5) UNSIGNED DEFAULT '0',
  `phenixStorage` smallint(5) UNSIGNED DEFAULT '0',
  `motherShip` tinyint(4) DEFAULT '0',
  `resourcesStorage` int(10) UNSIGNED DEFAULT '0',
  `uOrbitalBase` datetime DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `orbitalBaseBuildingQueue` (
  `id` int(10) UNSIGNED NOT NULL,
  `rOrbitalBase` int(10) UNSIGNED NOT NULL,
  `buildingNumber` tinyint(3) UNSIGNED NOT NULL,
  `targetLevel` tinyint(3) UNSIGNED NOT NULL,
  `dStart` datetime NOT NULL,
  `dEnd` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `orbitalBaseShipQueue` (
  `id` int(10) UNSIGNED NOT NULL,
  `rOrbitalBase` int(10) UNSIGNED NOT NULL,
  `dockType` tinyint(3) UNSIGNED NOT NULL,
  `shipNumber` tinyint(3) UNSIGNED NOT NULL,
  `quantity` smallint(5) UNSIGNED NOT NULL,
  `dStart` datetime NOT NULL,
  `dEnd` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `place` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED DEFAULT NULL,
  `rSystem` int(10) UNSIGNED NOT NULL,
  `typeOfPlace` tinyint(3) UNSIGNED NOT NULL,
  `position` tinyint(3) UNSIGNED NOT NULL,
  `population` float UNSIGNED NOT NULL,
  `coefResources` float UNSIGNED NOT NULL,
  `coefHistory` float UNSIGNED NOT NULL,
  `resources` int(10) UNSIGNED DEFAULT '0',
  `danger` tinyint(3) UNSIGNED DEFAULT '0',
  `maxDanger` tinyint(3) UNSIGNED DEFAULT '0',
  `uPlace` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DELIMITER $$
CREATE TRIGGER `savePlaceChange` BEFORE UPDATE ON `place` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

CREATE TABLE `player` (
  `id` int(10) UNSIGNED NOT NULL,
  `rColor` int(10) UNSIGNED NOT NULL,
  `rGodfather` int(10) UNSIGNED DEFAULT NULL,
  `bind` varchar(50) DEFAULT NULL,
  `name` varchar(25) NOT NULL,
  `avatar` varchar(12) NOT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT '1',
  `status` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `credit` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `experience` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `factionPoint` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `level` tinyint(3) UNSIGNED DEFAULT '0',
  `victory` int(10) UNSIGNED DEFAULT '0',
  `defeat` int(10) UNSIGNED DEFAULT '0',
  `premium` tinyint(4) NOT NULL DEFAULT '0',
  `statement` tinyint(4) NOT NULL DEFAULT '0',
  `description` text,
  `stepTutorial` tinyint(3) UNSIGNED DEFAULT NULL,
  `stepDone` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `iUniversity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `partNaturalSciences` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `partLifeSciences` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `partSocialPoliticalSciences` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `partInformaticEngineering` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `dInscription` datetime DEFAULT NULL,
  `dLastConnection` datetime DEFAULT NULL,
  `dLastActivity` datetime DEFAULT NULL,
  `uPlayer` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `playerRanking` (
  `id` int(10) UNSIGNED NOT NULL,
  `rRanking` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `general` int(10) UNSIGNED NOT NULL,
  `generalPosition` smallint(6) NOT NULL,
  `generalVariation` smallint(6) NOT NULL,
  `experience` int(10) UNSIGNED NOT NULL,
  `experiencePosition` smallint(6) NOT NULL,
  `experienceVariation` smallint(6) NOT NULL,
  `butcher` int(10) UNSIGNED NOT NULL,
  `butcherDestroyedPEV` int(10) UNSIGNED NOT NULL,
  `butcherLostPEV` int(10) UNSIGNED NOT NULL,
  `butcherPosition` smallint(6) NOT NULL,
  `butcherVariation` smallint(6) NOT NULL,
  `armies` int(10) UNSIGNED NOT NULL,
  `armiesPosition` smallint(6) NOT NULL,
  `armiesVariation` smallint(6) NOT NULL,
  `trader` int(10) UNSIGNED NOT NULL,
  `traderPosition` smallint(6) NOT NULL,
  `traderVariation` smallint(6) NOT NULL,
  `resources` int(10) UNSIGNED NOT NULL,
  `resourcesPosition` smallint(6) NOT NULL,
  `resourcesVariation` smallint(6) NOT NULL,
  `fight` int(10) UNSIGNED NOT NULL,
  `victories` int(10) UNSIGNED NOT NULL,
  `defeat` int(10) UNSIGNED NOT NULL,
  `fightPosition` smallint(6) NOT NULL,
  `fightVariation` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ranking` (
  `id` int(10) UNSIGNED NOT NULL,
  `dRanking` datetime NOT NULL,
  `player` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `faction` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `recyclingLog` (
  `id` int(10) UNSIGNED NOT NULL,
  `rRecycling` int(10) UNSIGNED NOT NULL,
  `resources` int(10) UNSIGNED NOT NULL,
  `credits` int(10) UNSIGNED NOT NULL,
  `ship0` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship1` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship2` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship3` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship4` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship5` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship6` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship7` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship8` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship9` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship10` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ship11` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `dLog` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `recyclingMission` (
  `id` int(10) UNSIGNED NOT NULL,
  `rBase` int(10) UNSIGNED NOT NULL,
  `rTarget` int(10) UNSIGNED NOT NULL,
  `cycleTime` int(10) UNSIGNED NOT NULL,
  `recyclerQuantity` smallint(5) UNSIGNED NOT NULL,
  `addToNextMission` smallint(5) UNSIGNED NOT NULL,
  `uRecycling` datetime NOT NULL,
  `statement` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `report` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayerAttacker` int(10) UNSIGNED NOT NULL,
  `rPlayerDefender` int(10) UNSIGNED NOT NULL,
  `rPlayerWinner` int(10) UNSIGNED NOT NULL,
  `resources` int(10) UNSIGNED NOT NULL,
  `expCom` int(10) UNSIGNED NOT NULL,
  `rPlace` int(10) UNSIGNED NOT NULL,
  `placeName` varchar(45) NOT NULL,
  `type` tinyint(3) UNSIGNED DEFAULT NULL,
  `isLegal` tinyint(3) UNSIGNED DEFAULT '1',
  `hasBeenPunished` tinyint(3) UNSIGNED DEFAULT '0',
  `round` int(10) UNSIGNED DEFAULT '0',
  `importance` int(10) UNSIGNED DEFAULT NULL,
  `pevInBeginA` smallint(5) UNSIGNED DEFAULT NULL,
  `pevInBeginD` smallint(5) UNSIGNED DEFAULT NULL,
  `pevAtEndA` smallint(5) UNSIGNED DEFAULT NULL,
  `pevAtEndD` smallint(5) UNSIGNED DEFAULT NULL,
  `avatarA` varchar(255) NOT NULL,
  `avatarD` varchar(255) NOT NULL,
  `nameA` varchar(255) NOT NULL,
  `nameD` varchar(255) NOT NULL,
  `levelA` smallint(5) UNSIGNED NOT NULL,
  `levelD` smallint(5) UNSIGNED NOT NULL,
  `experienceA` int(10) UNSIGNED NOT NULL,
  `experienceD` int(10) UNSIGNED NOT NULL,
  `palmaresA` smallint(5) UNSIGNED NOT NULL,
  `palmaresD` smallint(5) UNSIGNED NOT NULL,
  `expPlayerA` int(10) UNSIGNED NOT NULL,
  `expPlayerD` int(10) UNSIGNED NOT NULL,
  `statementAttacker` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `statementDefender` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `dFight` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `research` (
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `mathLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `physLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `chemLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bioLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `mediLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `econoLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `psychoLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `networkLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `algoLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `statLevel` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `naturalTech` tinyint(4) NOT NULL DEFAULT '0',
  `lifeTech` tinyint(4) NOT NULL DEFAULT '3',
  `socialTech` tinyint(4) NOT NULL DEFAULT '5',
  `informaticTech` tinyint(4) NOT NULL DEFAULT '7',
  `naturalToPay` int(10) UNSIGNED NOT NULL,
  `lifeToPay` int(10) UNSIGNED NOT NULL,
  `socialToPay` int(10) UNSIGNED NOT NULL,
  `informaticToPay` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roadMap` (
  `id` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `oContent` text NOT NULL,
  `pContent` text NOT NULL,
  `statement` tinyint(4) NOT NULL COMMENT '0 = caché, 1 = affiché',
  `dCreation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sector` (
  `id` int(10) UNSIGNED NOT NULL,
  `rColor` int(10) UNSIGNED NOT NULL,
  `rSurrender` int(10) UNSIGNED DEFAULT NULL,
  `xPosition` smallint(5) UNSIGNED DEFAULT NULL,
  `yPosition` smallint(5) UNSIGNED DEFAULT NULL,
  `xBarycentric` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `yBarycentric` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `tax` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `population` int(10) UNSIGNED NOT NULL,
  `lifePlanet` int(10) UNSIGNED DEFAULT NULL,
  `points` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  `prime` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DELIMITER $$
CREATE TRIGGER `saveSectorChange` BEFORE UPDATE ON `sector` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

CREATE TABLE `spyReport` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rPlace` int(10) UNSIGNED NOT NULL,
  `price` int(10) UNSIGNED NOT NULL,
  `placeColor` smallint(5) UNSIGNED NOT NULL,
  `typeOfBase` tinyint(3) UNSIGNED NOT NULL,
  `typeOfOrbitalBase` tinyint(3) UNSIGNED NOT NULL,
  `placeName` varchar(255) NOT NULL,
  `points` int(10) UNSIGNED NOT NULL,
  `rEnemy` int(10) UNSIGNED NOT NULL,
  `enemyName` varchar(255) NOT NULL,
  `enemyAvatar` varchar(255) NOT NULL,
  `enemyLevel` int(10) UNSIGNED NOT NULL,
  `resources` int(10) UNSIGNED NOT NULL,
  `shipsInStorage` text NOT NULL,
  `antiSpyInvest` int(11) NOT NULL,
  `commercialRouteIncome` int(11) NOT NULL,
  `commanders` text NOT NULL,
  `success` smallint(5) UNSIGNED NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `dSpying` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `squadron` (
  `id` int(10) UNSIGNED NOT NULL,
  `rCommander` int(10) UNSIGNED NOT NULL,
  `ship0` smallint(5) UNSIGNED DEFAULT '0',
  `ship1` smallint(5) UNSIGNED DEFAULT '0',
  `ship2` smallint(5) UNSIGNED DEFAULT '0',
  `ship3` smallint(5) UNSIGNED DEFAULT '0',
  `ship4` smallint(5) UNSIGNED DEFAULT '0',
  `ship5` smallint(5) UNSIGNED DEFAULT '0',
  `ship6` smallint(5) UNSIGNED DEFAULT '0',
  `ship7` smallint(5) UNSIGNED DEFAULT '0',
  `ship8` smallint(5) UNSIGNED DEFAULT '0',
  `ship9` smallint(5) UNSIGNED DEFAULT '0',
  `ship10` smallint(5) UNSIGNED DEFAULT '0',
  `ship11` smallint(5) UNSIGNED DEFAULT '0',
  `dCreation` datetime DEFAULT NULL,
  `dLastModification` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `squadronReport` (
  `id` int(10) UNSIGNED NOT NULL,
  `rReport` int(10) UNSIGNED NOT NULL,
  `rCommander` int(10) UNSIGNED DEFAULT NULL,
  `position` tinyint(3) UNSIGNED NOT NULL,
  `round` int(11) NOT NULL,
  `ship0` smallint(5) UNSIGNED NOT NULL,
  `ship1` smallint(5) UNSIGNED NOT NULL,
  `ship2` smallint(5) UNSIGNED NOT NULL,
  `ship3` smallint(5) UNSIGNED NOT NULL,
  `ship4` smallint(5) UNSIGNED NOT NULL,
  `ship5` smallint(5) UNSIGNED NOT NULL,
  `ship6` smallint(5) UNSIGNED NOT NULL,
  `ship7` smallint(5) UNSIGNED NOT NULL,
  `ship8` smallint(5) UNSIGNED NOT NULL,
  `ship9` smallint(5) UNSIGNED NOT NULL,
  `ship10` smallint(5) UNSIGNED NOT NULL,
  `ship11` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `system` (
  `id` int(10) UNSIGNED NOT NULL,
  `rSector` int(10) UNSIGNED NOT NULL,
  `rColor` int(10) UNSIGNED NOT NULL,
  `xPosition` smallint(5) UNSIGNED DEFAULT NULL,
  `yPosition` smallint(5) UNSIGNED DEFAULT NULL,
  `typeOfSystem` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DELIMITER $$
CREATE TRIGGER `saveSystemChange` BEFORE UPDATE ON `system` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

CREATE TABLE `technology` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `technology` smallint(5) UNSIGNED NOT NULL,
  `level` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `technologyQueue` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rPlace` int(10) UNSIGNED NOT NULL,
  `technology` smallint(5) UNSIGNED NOT NULL,
  `targetLevel` tinyint(3) UNSIGNED NOT NULL,
  `dStart` datetime NOT NULL,
  `dEnd` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `transaction` (
  `id` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rPlace` int(10) UNSIGNED NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0 = resource, 1 = ship, 2 = commander',
  `quantity` int(11) NOT NULL,
  `identifier` smallint(6) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `commercialShipQuantity` smallint(6) NOT NULL,
  `statement` tinyint(4) NOT NULL COMMENT '0 = proposed, 1 = completed, 2 = canceled',
  `dPublication` datetime NOT NULL,
  `dValidation` datetime DEFAULT NULL,
  `currentRate` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `vGalaxyDiary` (
`id` int(10) unsigned
,`sector` int(10) unsigned
,`oldColor` tinyint(3) unsigned
,`newColor` tinyint(3) unsigned
,`dChangement` datetime
);

CREATE TABLE `vote` (
  `id` int(10) UNSIGNED NOT NULL,
  `rCandidate` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `rElection` int(10) UNSIGNED NOT NULL,
  `dVotation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `voteLaw` (
  `id` int(10) UNSIGNED NOT NULL,
  `rLaw` int(10) UNSIGNED NOT NULL,
  `rPlayer` int(10) UNSIGNED NOT NULL,
  `vote` tinyint(4) NOT NULL,
  `dVotation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `vSectorDiary` (
`occurency` bigint(21)
,`id` int(10) unsigned
,`system` int(10) unsigned
,`sector` int(10) unsigned
,`oldColor` tinyint(3) unsigned
,`newColor` tinyint(3) unsigned
,`dChangement` datetime
);
CREATE TABLE `vSystemDiary` (
`id` int(10) unsigned
,`place` int(10) unsigned
,`oldPlayer` tinyint(3) unsigned
,`oldName` varchar(25)
,`oldColor` int(10) unsigned
,`newPlayer` tinyint(3) unsigned
,`newName` varchar(25)
,`newColor` int(10) unsigned
,`dChangement` datetime
,`position` tinyint(3) unsigned
,`system` int(10) unsigned
);
DROP TABLE IF EXISTS `vGalaxyDiary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vGalaxyDiary`  AS  select `h`.`id` AS `id`,`h`.`rSector` AS `sector`,`h`.`oldColor` AS `oldColor`,`h`.`newColor` AS `newColor`,`h`.`dChangement` AS `dChangement` from (`changeColorSector` `h` left join `system` `s` on((`h`.`rSector` = `s`.`id`))) order by `h`.`dChangement` desc limit 0,100 ;
DROP TABLE IF EXISTS `vSectorDiary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vSectorDiary`  AS  select count(`h`.`id`) AS `occurency`,`h`.`id` AS `id`,`h`.`rSystem` AS `system`,`s`.`rSector` AS `sector`,`h`.`oldColor` AS `oldColor`,`h`.`newColor` AS `newColor`,`h`.`dChangement` AS `dChangement` from (((`changeColorSystem` `h` left join `system` `s` on((`h`.`rSystem` = `s`.`id`))) left join `color` `c1` on((`h`.`oldColor` = `c1`.`id`))) left join `color` `c2` on((`h`.`newColor` = `c2`.`id`))) group by `c1`.`id`,`c2`.`id`,hour(`h`.`dChangement`) order by `h`.`dChangement` desc limit 0,1000 ;
DROP TABLE IF EXISTS `vSystemDiary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vSystemDiary`  AS  select `h`.`id` AS `id`,`h`.`rPlace` AS `place`,`h`.`oldPlayer` AS `oldPlayer`,`p1`.`name` AS `oldName`,`c1`.`id` AS `oldColor`,`h`.`newPlayer` AS `newPlayer`,`p2`.`name` AS `newName`,`c2`.`id` AS `newColor`,`h`.`dChangement` AS `dChangement`,`p`.`position` AS `position`,`p`.`rSystem` AS `system` from (((((`changeColorPlace` `h` left join `place` `p` on((`p`.`id` = `h`.`rPlace`))) left join `player` `p1` on((`h`.`oldPlayer` = `p1`.`id`))) left join `color` `c1` on((`p1`.`rColor` = `c1`.`id`))) left join `player` `p2` on((`h`.`newPlayer` = `p2`.`id`))) left join `color` `c2` on((`p2`.`rColor` = `c2`.`id`))) order by `h`.`dChangement` desc limit 0,5000 ;


ALTER TABLE `budget__donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkPlayerBindKey` (`player_bind_key`);

ALTER TABLE `candidate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkCandidateElection` (`rElection`),
  ADD KEY `fkCandidatePlayer` (`rPlayer`);

ALTER TABLE `changeColorPlace`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `changeColorSector`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `changeColorSystem`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `color`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `colorLink`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `commander`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkCommanderPlayer` (`rPlayer`),
  ADD KEY `fkCommanderBase` (`rBase`);

ALTER TABLE `commercialRoute`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkCommercialRouteOrbitalBaseA` (`rOrbitalBase`),
  ADD KEY `fkCommercialRouteOrbitalBaseB` (`rOrbitalBaseLinked`);

ALTER TABLE `commercialShipping`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `commercialTax`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `conversationMessage`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `conversationUser`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `creditTransaction`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `election`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkElectionColor` (`rColor`);

ALTER TABLE `factionNews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkFactionNewsFaction` (`rFaction`);

ALTER TABLE `factionRanking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkFactionRankingRanking` (`rRanking`),
  ADD KEY `fkFactionRankingPlayer` (`rFaction`);

ALTER TABLE `forumLastView`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkForumLastViewsPlayer` (`rPlayer`),
  ADD KEY `fkForumLastViewsTopic` (`rTopic`);

ALTER TABLE `forumMessage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkForumMessagePlayer` (`rPlayer`),
  ADD KEY `fkForumMessageTopic` (`rTopic`);

ALTER TABLE `forumTopic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkForumTopicColor` (`rColor`),
  ADD KEY `fkForumTopicPlayer` (`rPlayer`);

ALTER TABLE `law`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkLawColor` (`rColor`);

ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkNotificationPlayer` (`rPlayer`);

ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `news__military`
  ADD KEY `fkMilitaryNews` (`news_id`),
  ADD KEY `fkAttacker` (`attacker_id`),
  ADD KEY `fkDefender` (`defender_id`),
  ADD KEY `fkPlace` (`place_id`);

ALTER TABLE `news__politics`
  ADD KEY `fkPoliticsNews` (`news_id`),
  ADD KEY `fkFaction` (`faction_id`);

ALTER TABLE `news__trade`
  ADD KEY `fkTradeNews` (`news_id`),
  ADD KEY `fkTransaction` (`transaction_id`);

ALTER TABLE `orbitalBase`
  ADD PRIMARY KEY (`rPlace`),
  ADD KEY `fkOrbitalBasePlayer` (`rPlayer`);

ALTER TABLE `orbitalBaseBuildingQueue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkOrbitalBaseBuildingQueueOrbitalBase` (`rOrbitalBase`);

ALTER TABLE `orbitalBaseShipQueue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkOrbitalBaseShipQueueOrbitalBase` (`rOrbitalBase`);

ALTER TABLE `place`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkPlacePlayer` (`rPlayer`),
  ADD KEY `fkPlaceSystem` (`rSystem`);

ALTER TABLE `player`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`),
  ADD KEY `fkBindKey` (`bind`),
  ADD KEY `fkPlayerColor` (`rColor`),
  ADD KEY `fkPlayerPlayer` (`rGodfather`);

ALTER TABLE `playerRanking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkPlayerRankingRanking` (`rRanking`),
  ADD KEY `fkPlayerRankingPlayer` (`rPlayer`);

ALTER TABLE `ranking`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `recyclingLog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkRecyclingLogRec` (`rRecycling`);

ALTER TABLE `recyclingMission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkRecyclingMissionOB` (`rBase`);

ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkReportPlayerA` (`rPlayerAttacker`),
  ADD KEY `fkReportPlayerD` (`rPlayerDefender`),
  ADD KEY `fkReportPlayerW` (`rPlayerWinner`);

ALTER TABLE `research`
  ADD PRIMARY KEY (`rPlayer`);

ALTER TABLE `roadMap`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sector`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkSectorColor` (`rColor`);

ALTER TABLE `spyReport`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkSpyReportPlayer` (`rPlayer`),
  ADD KEY `fkSpyReportPlace` (`rPlace`);

ALTER TABLE `squadron`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkSquadronCommander` (`rCommander`);

ALTER TABLE `squadronReport`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkSquadronReportReport` (`rReport`),
  ADD KEY `fkSquadronReportCommander` (`rCommander`);

ALTER TABLE `system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkSystemSector` (`rSector`),
  ADD KEY `fkSystemColor` (`rColor`);

ALTER TABLE `technology`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkTechnologyPlayer` (`rPlayer`);

ALTER TABLE `technologyQueue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkTechnologyQueuePlayer` (`rPlayer`),
  ADD KEY `fkTechnologyQueueOB` (`rPlace`);

ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkTransactionPlayer` (`rPlayer`);

ALTER TABLE `vote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkVoteCandidate` (`rCandidate`),
  ADD KEY `fkVotePlayer` (`rPlayer`),
  ADD KEY `fkVoteElection` (`rElection`);

ALTER TABLE `voteLaw`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkVoteLawLaw` (`rLaw`),
  ADD KEY `fkVoteLawPlayer` (`rPlayer`);


ALTER TABLE `budget__donations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `candidate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `changeColorPlace`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `changeColorSector`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `changeColorSystem`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `colorLink`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
ALTER TABLE `commander`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `commercialRoute`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `commercialShipping`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `commercialTax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
ALTER TABLE `conversation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `conversationMessage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `conversationUser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `creditTransaction`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `election`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `factionNews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `factionRanking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `forumLastView`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `forumMessage`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `forumTopic`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `law`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
ALTER TABLE `orbitalBaseBuildingQueue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `orbitalBaseShipQueue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `place`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20302;
ALTER TABLE `player`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
ALTER TABLE `playerRanking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `ranking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
ALTER TABLE `recyclingLog`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
ALTER TABLE `recyclingMission`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `report`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `roadMap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `sector`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
ALTER TABLE `spyReport`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `squadron`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
ALTER TABLE `squadronReport`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
ALTER TABLE `system`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4141;
ALTER TABLE `technology`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;
ALTER TABLE `technologyQueue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `transaction`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `vote`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `voteLaw`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `budget__donations`
  ADD CONSTRAINT `fkPlayerBindKey` FOREIGN KEY (`player_bind_key`) REFERENCES `player` (`bind`);

ALTER TABLE `candidate`
  ADD CONSTRAINT `fkCandidateElection` FOREIGN KEY (`rElection`) REFERENCES `election` (`id`),
  ADD CONSTRAINT `fkCandidatePlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `commander`
  ADD CONSTRAINT `fkCommanderBase` FOREIGN KEY (`rBase`) REFERENCES `orbitalBase` (`rPlace`),
  ADD CONSTRAINT `fkCommanderPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `commercialRoute`
  ADD CONSTRAINT `fkCommercialRouteOrbitalBaseA` FOREIGN KEY (`rOrbitalBase`) REFERENCES `orbitalBase` (`rPlace`),
  ADD CONSTRAINT `fkCommercialRouteOrbitalBaseB` FOREIGN KEY (`rOrbitalBaseLinked`) REFERENCES `orbitalBase` (`rPlace`);

ALTER TABLE `election`
  ADD CONSTRAINT `fkElectionColor` FOREIGN KEY (`rColor`) REFERENCES `color` (`id`);

ALTER TABLE `factionNews`
  ADD CONSTRAINT `fkFactionNewsFaction` FOREIGN KEY (`rFaction`) REFERENCES `color` (`id`);

ALTER TABLE `factionRanking`
  ADD CONSTRAINT `fkFactionRankingPlayer` FOREIGN KEY (`rFaction`) REFERENCES `color` (`id`),
  ADD CONSTRAINT `fkFactionRankingRanking` FOREIGN KEY (`rRanking`) REFERENCES `ranking` (`id`);

ALTER TABLE `forumLastView`
  ADD CONSTRAINT `fkForumLastViewsPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkForumLastViewsTopic` FOREIGN KEY (`rTopic`) REFERENCES `forumTopic` (`id`);

ALTER TABLE `forumMessage`
  ADD CONSTRAINT `fkForumMessagePlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkForumMessageTopic` FOREIGN KEY (`rTopic`) REFERENCES `forumTopic` (`id`);

ALTER TABLE `forumTopic`
  ADD CONSTRAINT `fkForumTopicColor` FOREIGN KEY (`rColor`) REFERENCES `color` (`id`),
  ADD CONSTRAINT `fkForumTopicPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `law`
  ADD CONSTRAINT `fkLawColor` FOREIGN KEY (`rColor`) REFERENCES `color` (`id`);

ALTER TABLE `notification`
  ADD CONSTRAINT `fkNotificationPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `news__military`
  ADD CONSTRAINT `fkMilitaryNews` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`),
  ADD CONSTRAINT `fkAttacker` FOREIGN KEY (`attacker_id`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkDefender` FOREIGN KEY (`defender_id`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkPlace` FOREIGN KEY (`place_id`) REFERENCES `place` (`id`);

ALTER TABLE `news__politics`
  ADD CONSTRAINT `fkPoliticsNews` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`),
  ADD CONSTRAINT `fkFaction` FOREIGN KEY (`faction_id`) REFERENCES `color` (`id`);

ALTER TABLE `news__trade`
  ADD CONSTRAINT `fkTradeNews` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`),
  ADD CONSTRAINT `fkTransaction` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`id`);

ALTER TABLE `orbitalBase`
  ADD CONSTRAINT `fkOrbitalBasePlace` FOREIGN KEY (`rPlace`) REFERENCES `place` (`id`),
  ADD CONSTRAINT `fkOrbitalBasePlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `orbitalBaseBuildingQueue`
  ADD CONSTRAINT `fkOrbitalBaseBuildingQueueOrbitalBase` FOREIGN KEY (`rOrbitalBase`) REFERENCES `orbitalBase` (`rPlace`);

ALTER TABLE `orbitalBaseShipQueue`
  ADD CONSTRAINT `fkOrbitalBaseShipQueueOrbitalBase` FOREIGN KEY (`rOrbitalBase`) REFERENCES `orbitalBase` (`rPlace`);

ALTER TABLE `place`
  ADD CONSTRAINT `fkPlacePlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkPlaceSystem` FOREIGN KEY (`rSystem`) REFERENCES `system` (`id`);

ALTER TABLE `player`
  ADD CONSTRAINT `fkPlayerColor` FOREIGN KEY (`rColor`) REFERENCES `color` (`id`),
  ADD CONSTRAINT `fkPlayerPlayer` FOREIGN KEY (`rGodfather`) REFERENCES `player` (`id`);

ALTER TABLE `playerRanking`
  ADD CONSTRAINT `fkPlayerRankingPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkPlayerRankingRanking` FOREIGN KEY (`rRanking`) REFERENCES `ranking` (`id`);

ALTER TABLE `recyclingLog`
  ADD CONSTRAINT `fkRecyclingLogRec` FOREIGN KEY (`rRecycling`) REFERENCES `recyclingMission` (`id`) ON DELETE CASCADE;

ALTER TABLE `recyclingMission`
  ADD CONSTRAINT `fkRecyclingMissionOB` FOREIGN KEY (`rBase`) REFERENCES `orbitalBase` (`rPlace`);

ALTER TABLE `report`
  ADD CONSTRAINT `fkReportPlayerA` FOREIGN KEY (`rPlayerAttacker`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkReportPlayerD` FOREIGN KEY (`rPlayerDefender`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkReportPlayerW` FOREIGN KEY (`rPlayerWinner`) REFERENCES `player` (`id`);

ALTER TABLE `research`
  ADD CONSTRAINT `fkResearchPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `sector`
  ADD CONSTRAINT `fkSectorColor` FOREIGN KEY (`rColor`) REFERENCES `color` (`id`);

ALTER TABLE `spyReport`
  ADD CONSTRAINT `fkSpyReportPlace` FOREIGN KEY (`rPlace`) REFERENCES `place` (`id`),
  ADD CONSTRAINT `fkSpyReportPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `squadron`
  ADD CONSTRAINT `fkSquadronCommander` FOREIGN KEY (`rCommander`) REFERENCES `commander` (`id`);

ALTER TABLE `squadronReport`
  ADD CONSTRAINT `fkSquadronReportCommander` FOREIGN KEY (`rCommander`) REFERENCES `commander` (`id`),
  ADD CONSTRAINT `fkSquadronReportReport` FOREIGN KEY (`rReport`) REFERENCES `report` (`id`);

ALTER TABLE `system`
  ADD CONSTRAINT `fkSystemColor` FOREIGN KEY (`rColor`) REFERENCES `color` (`id`),
  ADD CONSTRAINT `fkSystemSector` FOREIGN KEY (`rSector`) REFERENCES `sector` (`id`);

ALTER TABLE `technology`
  ADD CONSTRAINT `fkTechnologyPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `technologyQueue`
  ADD CONSTRAINT `fkTechnologyQueueOB` FOREIGN KEY (`rPlace`) REFERENCES `orbitalBase` (`rPlace`),
  ADD CONSTRAINT `fkTechnologyQueuePlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `transaction`
  ADD CONSTRAINT `fkTransactionPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `vote`
  ADD CONSTRAINT `fkVoteCandidate` FOREIGN KEY (`rCandidate`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fkVoteElection` FOREIGN KEY (`rElection`) REFERENCES `election` (`id`),
  ADD CONSTRAINT `fkVotePlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);

ALTER TABLE `voteLaw`
  ADD CONSTRAINT `fkVoteLawLaw` FOREIGN KEY (`rLaw`) REFERENCES `law` (`id`),
  ADD CONSTRAINT `fkVoteLawPlayer` FOREIGN KEY (`rPlayer`) REFERENCES `player` (`id`);
COMMIT;

