<?php
echo '<h1>suppression de la table commander</h1>';
echo '<h1>suppression de la table squadron</h1>';
echo '<h1>création de la nouvelle table commander</h1>';
echo '<h1>création de la nouvelle table squadron</h1>';
echo '<h1>ajout de la table travel</h1>';

$db = DataBaseAdmin::getInstance();

$qr = $db->prepare("DROP TABLE commander");
$qr->execute();

$qr = $db->prepare("DROP TABLE squadron");
$qr->execute();

$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `commander` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rPlayer` int(10) unsigned NOT NULL,
  `rBase` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8_bin NOT NULL,
  `comment` text COLLATE utf8_bin,
  `sexe` tinyint(1) NOT NULL DEFAULT '1',
  `age` int(10) unsigned NOT NULL DEFAULT '20',
  `avatar` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `experience` int(10) unsigned NOT NULL DEFAULT '1',
  `uCommander` datetime DEFAULT NULL,
  `palmares` int(10) unsigned NOT NULL DEFAULT '0',
  `statement` tinyint(1) DEFAULT '0',
  `dCreation` datetime DEFAULT NULL,
  `dAffectation` datetime DEFAULT NULL,
  `dDeath` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_commander_player1` (`rPlayer`),
  KEY `rBase` (`rBase`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");
$qr->execute();

$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `squadron` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rCommander` int(10) unsigned NOT NULL,
  `ship0` tinyint(3) unsigned DEFAULT '0',
  `ship1` tinyint(3) unsigned DEFAULT '0',
  `ship2` tinyint(3) unsigned DEFAULT '0',
  `ship3` tinyint(3) unsigned DEFAULT '0',
  `ship4` tinyint(3) unsigned DEFAULT '0',
  `ship5` tinyint(3) unsigned DEFAULT '0',
  `ship6` tinyint(3) unsigned DEFAULT '0',
  `ship7` tinyint(3) unsigned DEFAULT '0',
  `ship8` tinyint(3) unsigned DEFAULT '0',
  `ship9` tinyint(3) unsigned DEFAULT '0',
  `ship10` tinyint(3) unsigned DEFAULT '0',
  `ship11` tinyint(3) unsigned DEFAULT '0',
  `dCreation` datetime DEFAULT NULL,
  `dLastModification` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_squadron_commander1` (`rCommander`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");
$qr->execute();

$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `travel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rCommander` int(11) NOT NULL,
  `rStartPlace` int(11) NOT NULL,
  `rDestinationPlace` int(11) NOT NULL,
  `dStart` datetime NOT NULL,
  `dArrival` datetime NOT NULL,
  `ressources` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `length` int(11) NOT NULL,
  `statement` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");
$qr->execute();
?>