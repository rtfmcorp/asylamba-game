<?php
echo '<h1>Module Arès</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table commander</h2>';

$db->query("DROP TABLE IF EXISTS `commander`");
$db->query("CREATE TABLE IF NOT EXISTS `commander` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rPlayer` int(10) unsigned NOT NULL,
  `rBase` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `comment` text,
  `sexe` tinyint(1) NOT NULL DEFAULT '1',
  `age` int(10) unsigned NOT NULL DEFAULT '20',
  `avatar` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `experience` int(10) unsigned NOT NULL DEFAULT '1',
  `uCommander` datetime DEFAULT NULL,
  `palmares` int(10) unsigned NOT NULL DEFAULT '0',
  `statement` tinyint(1) DEFAULT '0',
  `line` int(11) DEFAULT NULL,
  `rStartPlace` int(11) DEFAULT NULL,
  `rDestinationPlace` int(11) DEFAULT NULL,
  `dStart` datetime DEFAULT NULL,
  `dArrival` datetime DEFAULT NULL,
  `resources` int(11) DEFAULT NULL,
  `travelType` tinyint(4) DEFAULT NULL,
  `travelLength` tinyint(4) DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL,
  `dAffectation` datetime DEFAULT NULL,
  `dDeath` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_commander_player1` (`rPlayer`),
  KEY `rBase` (`rBase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table squadron</h2>';

$db->query("DROP TABLE IF EXISTS `squadron`");
$db->query("CREATE TABLE IF NOT EXISTS `squadron` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table report</h2>';

$db->query("DROP TABLE IF EXISTS `report`");
$db->query("CREATE TABLE IF NOT EXISTS `report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rPlayerAttacker` int(10) unsigned NOT NULL,
  `rPlayerDefender` int(10) unsigned NOT NULL,
  `rPlayerWinner` int(10) unsigned NOT NULL,
  `resources` int(11) NOT NULL,
  `expCom` int(11) NOT NULL,
  `expPlayerA` int(11) NOT NULL,
  `expPlayerD` int(11) NOT NULL,
  `rPlace` int(10) unsigned NOT NULL,
  `placeName` varchar(45) NOT NULL,
  `type` tinyint(3) unsigned DEFAULT NULL,
  `round` int(11) DEFAULT '0',
  `importance` int(11) DEFAULT NULL,
  `statementAttacker` int(11) NOT NULL DEFAULT '0',
  `statementDefender` int(11) NOT NULL DEFAULT '0',
  `dFight` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table squadronReport</h2>';

$db->query("DROP TABLE IF EXISTS `squadronReport`");
$db->query("CREATE TABLE IF NOT EXISTS `squadronReport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` tinyint(11) NOT NULL,
  `rReport` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `rCommander` int(11) NOT NULL,
  `ship0` tinyint(11) NOT NULL,
  `ship1` tinyint(11) NOT NULL,
  `ship2` tinyint(11) NOT NULL,
  `ship3` tinyint(11) NOT NULL,
  `ship4` tinyint(11) NOT NULL,
  `ship5` tinyint(11) NOT NULL,
  `ship6` tinyint(11) NOT NULL,
  `ship7` tinyint(11) NOT NULL,
  `ship8` tinyint(11) NOT NULL,
  `ship9` tinyint(11) NOT NULL,
  `ship10` tinyint(11) NOT NULL,
  `ship11` tinyint(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<br /><hr />';
?>