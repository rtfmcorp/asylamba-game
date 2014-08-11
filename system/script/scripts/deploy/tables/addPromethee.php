<?php
echo '<h1>Module Prométhée</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table research</h2>';

$db->query("DROP TABLE IF EXISTS `research`");
$db->query("CREATE TABLE IF NOT EXISTS `research` (
  `rPlayer` int(10) unsigned NOT NULL,
  `mathLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `physLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `chemLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bioLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mediLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `econoLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `psychoLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `networkLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `algoLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `statLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `naturalTech` tinyint(4) NOT NULL DEFAULT '0',
  `lifeTech` tinyint(4) NOT NULL DEFAULT '3',
  `socialTech` tinyint(4) NOT NULL DEFAULT '5',
  `informaticTech` tinyint(4) NOT NULL DEFAULT '7',
  `naturalToPay` int(11) unsigned NOT NULL,
  `lifeToPay` int(11) unsigned NOT NULL,
  `socialToPay` int(11) unsigned NOT NULL,
  `informaticToPay` int(11) unsigned NOT NULL,
  KEY `rPlayer` (`rPlayer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table technology</h2>';

$db->query("DROP TABLE IF EXISTS `technology`");
$db->query("CREATE TABLE IF NOT EXISTS `technology` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rPlayer` int(10) unsigned NOT NULL,
  `technology` smallint(5) unsigned NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table technologyQueue</h2>';

$db->query("DROP TABLE IF EXISTS `technologyQueue`");
$db->query("CREATE TABLE IF NOT EXISTS `technologyQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rPlace` int(11) NOT NULL,
  `technology` smallint(5) unsigned NOT NULL,
  `targetLevel` tinyint(3) unsigned NOT NULL,
  `dStart` datetime NOT NULL,
  `dEnd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");



echo '<br /><hr />';
?>