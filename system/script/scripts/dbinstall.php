<?php
$db = DataBaseAdmin::getInstance();

if (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'clear') {
	$db->query("DROP TABLE IF EXISTS `bugTracker`;");
	$db->query("DROP TABLE IF EXISTS `commercialShipping`;");
	$db->query("DROP TABLE IF EXISTS `candidate`;");
	$db->query("DROP TABLE IF EXISTS `election`;");
	$db->query("DROP TABLE IF EXISTS `vote`;");
	$db->query("DROP TABLE IF EXISTS `color`;");
	$db->query("DROP TABLE IF EXISTS `place`;");
	$db->query("DROP TABLE IF EXISTS `system`;");
	$db->query("DROP TABLE IF EXISTS `sector`;");
}

# bugTracker
$db->query("CREATE TABLE IF NOT EXISTS `bugTracker` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`url` varchar(150) NOT NULL,
	`rPlayer` int(11) NOT NULL,
	`bindKey` varchar(30) NOT NULL,
	`type` smallint(6) NOT NULL,
	`dSending` datetime NOT NULL,
	`message` text NOT NULL,
	`statement` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = waiting, 1 = archived, 2 = deleted',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

# commercialShipping
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

# election
$db->query("CREATE TABLE IF NOT EXISTS `candidate` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rElection` int(11) NOT NULL,
	`rPlayer` int(11) NOT NULL,
	`program` text COLLATE utf8_bin,
	`dPresentation` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;");

$db->query("CREATE TABLE IF NOT EXISTS `election` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rColor` int(11) NOT NULL,
	`dElection` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;");

$db->query("CREATE TABLE IF NOT EXISTS `vote` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rCandidate` int(11) NOT NULL,
	`rPlayer` int(11) NOT NULL,
	`rElection` int(11) NOT NULL,
	`dVotation` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;");

$db->query("CREATE TABLE IF NOT EXISTS `color` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`alive` tinyint(1) NOT NULL DEFAULT '1',
	`credits` int(11) NOT NULL,
	`players` int(11) NOT NULL,
	`activePlayers` int(11) NOT NULL,
	`points` int(11) NOT NULL,
	`sectors` smallint(11) NOT NULL,
	`electionStatement` tinyint(11) NOT NULL,
	`dLastElection` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

$db->query("INSERT INTO `color` (`id`, `alive`, `credits`, `players`, `activePlayers`, `points`, `sectors`, `electionStatement`, `dLastElection`) VALUES
(1, 1, 0, 0, 0, 0, 0, 0, NULL),
(2, 1, 0, 0, 0, 0, 0, 0, NULL),
(3, 1, 0, 0, 0, 0, 0, 0, NULL),
(4, 1, 0, 0, 0, 0, 0, 0, NULL),
(5, 1, 0, 0, 0, 0, 0, 0, NULL),
(6, 1, 0, 0, 0, 0, 0, 0, NULL),
(7, 1, 0, 0, 0, 0, 0, 0, NULL);");


$db->query("CREATE TABLE IF NOT EXISTS `place` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` int(10) unsigned NOT NULL,
	`rSystem` int(10) unsigned NOT NULL,
	`typeOfPlace` tinyint(3) unsigned NOT NULL,
	`position` tinyint(3) unsigned NOT NULL,
	`population` float unsigned NOT NULL,
	`coefResources` float unsigned NOT NULL,
	`coefHistory` float unsigned NOT NULL,
	`resources` int(10) unsigned DEFAULT '0',
	`uResources` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;");

$db->query("CREATE TABLE IF NOT EXISTS `system` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`rSector` int(10) unsigned NOT NULL,
	`rColor` int(10) unsigned NOT NULL,
	`xPosition` smallint(5) unsigned DEFAULT NULL,
	`yPosition` smallint(5) unsigned DEFAULT NULL,
	`typeOfSystem` smallint(5) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

$db->query("CREATE TABLE IF NOT EXISTS `sector` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`rColor` int(10) unsigned NOT NULL,
	`xPosition` smallint(5) unsigned DEFAULT NULL,
	`yPosition` smallint(5) unsigned DEFAULT NULL,
	`xBarycentric` smallint(5) unsigned NOT NULL DEFAULT '0',
	`yBarycentric` smallint(5) unsigned NOT NULL DEFAULT '0',
	`tax` smallint(5) unsigned NOT NULL DEFAULT '5',
	`population` int(11) NOT NULL,
	`lifePlanet` int(10) unsigned DEFAULT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

include_once GAIA;
GalaxyGenerator::generate();
echo GalaxyGenerator::getLog();
?>