<?php

$db = DataBaseAdmin::getInstance();

echo '<h1>Ajout de la table candidate</h1>';

$db->query("CREATE TABLE IF NOT EXISTS `candidate` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rElection` int(11) NOT NULL,
	`rPlayer` int(11) NOT NULL,
	`program` text COLLATE utf8_bin,
	`dPresentation` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;");

echo '<h1>Ajout de la table userRanking</h1>';

$db->query("CREATE TABLE IF NOT EXISTS `election` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rColor` int(11) NOT NULL,
	`dElection` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;");

echo '<h1>Ajout de la table vote</h1>';

$db->query("CREATE TABLE IF NOT EXISTS `vote` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rCandidate` int(11) NOT NULL,
	`rPlayer` int(11) NOT NULL,
	`rElection` int(11) NOT NULL,
	`dVotation` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;");