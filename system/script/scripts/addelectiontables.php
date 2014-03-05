<?php
#bool erase

$erase = Utils::getHTTPData('erase');

if ($erase) {
$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("
DROP TABLE IF EXISTS `candidate`;
DROP TABLE IF EXISTS `election`;
DROP TABLE IF EXISTS `vote`;
DROP TABLE IF EXISTS `color`;
");

echo '<h1>suppression des table candidate, election, vote, color si existantes</h1>';

$qr->execute();
}

echo '<h1>Ajout des table candidate, election, vote, color si non existantes</h1>';


$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `candidate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rElection` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `program` text COLLATE utf8_bin,
  `dPresentation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `election` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rColor` int(11) NOT NULL,
  `dElection` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rCandidate` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `rElection` int(11) NOT NULL,
  `dVotation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `color` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$qr->execute();
?>