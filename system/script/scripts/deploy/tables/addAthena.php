<?php
echo '<h1>Module Athena</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table orbitalBase</h2>';

$db->query("DROP TABLE IF EXISTS `orbitalBase`");
$db->query("CREATE TABLE IF NOT EXISTS `orbitalBase` (
  `rPlace` int(10) unsigned NOT NULL,
  `rPlayer` int(10) unsigned NOT NULL,
  `name` varchar(45) COLLATE utf8_bin NOT NULL,
  `typeOfBase` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `levelGenerator` tinyint(3) unsigned DEFAULT '0',
  `levelRefinery` tinyint(3) unsigned DEFAULT '0',
  `levelDock1` tinyint(3) unsigned DEFAULT '0',
  `levelDock2` tinyint(3) unsigned DEFAULT '0',
  `levelDock3` tinyint(3) unsigned DEFAULT '0',
  `levelTechnosphere` tinyint(3) unsigned DEFAULT '0',
  `levelCommercialPlateforme` tinyint(3) unsigned DEFAULT '0',
  `levelGravitationalModule` tinyint(3) unsigned DEFAULT '0',
  `points` int(10) unsigned DEFAULT '0',
  `iSchool` int(10) unsigned DEFAULT '0',
  `iAntiSpy` int(11) unsigned DEFAULT '0',
  `antiSpyAverage` int(10) unsigned DEFAULT '0',
  `pegaseStorage` smallint(5) unsigned DEFAULT '0',
  `satyreStorage` smallint(5) unsigned DEFAULT '0',
  `sireneStorage` smallint(5) unsigned DEFAULT '0',
  `dryadeStorage` smallint(5) unsigned DEFAULT '0',
  `chimereStorage` smallint(5) unsigned DEFAULT '0',
  `meduseStorage` smallint(5) unsigned DEFAULT '0',
  `griffonStorage` smallint(5) unsigned DEFAULT '0',
  `cyclopeStorage` smallint(5) unsigned DEFAULT '0',
  `minotaureStorage` smallint(5) unsigned DEFAULT '0',
  `hydreStorage` smallint(5) unsigned DEFAULT '0',
  `cerbereStorage` smallint(5) unsigned DEFAULT '0',
  `phenixStorage` smallint(5) unsigned DEFAULT '0',
  `motherShip` tinyint(1) DEFAULT '0',
  `isProductionRefinery` tinyint(1) DEFAULT '1',
  `resourcesStorage` bigint(20) unsigned DEFAULT '0',
  `uOrbitalBase` datetime DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL,
  PRIMARY KEY (`rPlace`),
  UNIQUE KEY `id_UNIQUE` (`rPlace`),
  KEY `fk_base_player1` (`rPlayer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table motherShip</h2>';

$db->query("DROP TABLE IF EXISTS `motherShip`");
$db->query("CREATE TABLE IF NOT EXISTS `motherShip` (
  `id` varchar(20) NOT NULL,
  `rPlayer` int(10) unsigned DEFAULT NULL,
  `rPlace` int(10) unsigned DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `levelRefinery` tinyint(3) unsigned DEFAULT '0',
  `levelDock` tinyint(3) unsigned DEFAULT '0',
  `levelHistoricalCenter` tinyint(3) unsigned DEFAULT '0',
  `levelGateway` tinyint(3) unsigned DEFAULT '0',
  `pegaseStorage` smallint(5) unsigned DEFAULT '0',
  `satyreStorage` smallint(5) unsigned DEFAULT '0',
  `chimereStorage` smallint(5) unsigned DEFAULT '0',
  `sireneStorage` smallint(5) unsigned DEFAULT '0',
  `dryadeStorage` smallint(5) unsigned DEFAULT '0',
  `meduseStorage` smallint(5) unsigned DEFAULT '0',
  `resourcesStorage` bigint(20) unsigned DEFAULT '0',
  `isProductionRefinery` tinyint(1) DEFAULT '1',
  `isProductionDock` tinyint(1) DEFAULT '0',
  `statement` tinyint(4) DEFAULT '0',
  `uBuildingQueue` datetime DEFAULT NULL,
  `uShipQueue` datetime DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `fk_motherShip_player1` (`rPlayer`),
  KEY `fk_motherShip_place1` (`rPlace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commercialRoute</h2>';

$db->query("DROP TABLE IF EXISTS `commercialRoute`");
$db->query("CREATE TABLE IF NOT EXISTS `commercialRoute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rOrbitalBase` int(11) NOT NULL,
  `rOrbitalBaseLinked` int(11) NOT NULL,
  `imageLink` varchar(10) NOT NULL,
  `distance` int(10) unsigned NOT NULL,
  `price` int(10) unsigned NOT NULL,
  `income` int(10) unsigned NOT NULL,
  `dProposition` datetime DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL,
  `statement` tinyint(3) unsigned NOT NULL COMMENT '0 = pas acceptée\n1 = active',
  PRIMARY KEY (`id`),
  KEY `fk_commercialPlateforme_orbitalBase1` (`rOrbitalBase`),
  KEY `fk_commercialPlateforme_orbitalBase2` (`rOrbitalBaseLinked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table orbitalBaseBuildingQueue</h2>';

$db->query("DROP TABLE IF EXISTS `orbitalBaseBuildingQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `orbitalBaseBuildingQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rOrbitalBase` int(11) NOT NULL,
  `buildingNumber` tinyint(3) unsigned NOT NULL,
  `targetLevel` tinyint(3) unsigned NOT NULL,
  `dStart` datetime NOT NULL,
  `dEnd` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orbitalBaseBuildingQueue_orbitalBase1` (`rOrbitalBase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table orbitalBaseShipQueue</h2>';

$db->query("DROP TABLE IF EXISTS `orbitalBaseShipQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `orbitalBaseShipQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rOrbitalBase` int(11) NOT NULL,
  `dockType` tinyint(3) unsigned NOT NULL,
  `shipNumber` tinyint(3) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `dStart` datetime NOT NULL,
  `dEnd` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orbitalBaseShipQueue_orbitalBase1` (`rOrbitalBase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table transaction</h2>';

$db->query("DROP TABLE IF EXISTS `transaction`");
$db->query("CREATE TABLE IF NOT EXISTS `transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rPlace` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0 = resource, 1 = ship, 2 = commander',
  `quantity` int(11) NOT NULL,
  `identifier` int(11) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `commercialShipQuantity` int(11) NOT NULL,
  `statement` tinyint(4) NOT NULL COMMENT '0 = proposed, 1 = completed, 2 = canceled',
  `dPublication` datetime NOT NULL,
  `dValidation` datetime DEFAULT NULL,
  `currentRate` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<h3>Remplissage de la table transaction</h3>';
include_once ATHENA;
$qr = $db->prepare("INSERT INTO `transaction` (
  `rPlayer`, 
  `rPlace`, 
  `type`, 
  `quantity`, 
  `identifier`, 
  `price`, 
  `commercialShipQuantity`, 
  `statement`, 
  `dPublication`, 
  `dValidation`, 
  `currentRate`) VALUES
(0, 0, ?, 8, NULL, 10, 0, ?, ?, ?, ?),
(0, 0, ?, 1, NULL, 12, 0, ?, ?, ?, ?),
(0, 0, ?, 8, NULL, 15, 0, ?, ?, ?, ?);");
$qr->execute(array(Transaction::TYP_RESOURCE, Transaction::ST_COMPLETED, Utils::now(), Utils::now(), 1.26,
  Transaction::TYP_COMMANDER, Transaction::ST_COMPLETED, Utils::now(), Utils::now(), 12,
  Transaction::TYP_SHIP, Transaction::ST_COMPLETED, Utils::now(), Utils::now(), 1.875));

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commercialShipping</h2>';

$db->query("DROP TABLE IF EXISTS `commercialShipping`");
$db->query("CREATE TABLE IF NOT EXISTS `commercialShipping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rBase` int(11) NOT NULL,
  `rBaseDestination` int(11) NOT NULL,
  `rTransaction` int(11) DEFAULT NULL,
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

echo '<br /><hr />';
?>