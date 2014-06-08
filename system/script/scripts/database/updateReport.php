<?php
echo '<h1>suppression de la table bigreport</h1>';
echo '<h1>suppression de la table report</h1>';
echo '<h1>création de la nouvelle table report</h1>';
echo '<h1>création de la nouvelle table squadronReport</h1>';

$db = DataBaseAdmin::getInstance();

$qr = $db->prepare("DROP TABLE bigreport");
$qr->execute();

$qr = $db->prepare("DROP TABLE report");
$qr->execute();

$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `squadronReport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` tinyint(11) NOT NULL,
  `rReport` int(11) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");
$qr->execute();

$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `report` (
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
$qr->execute();
?>