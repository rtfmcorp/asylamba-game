<?php
echo '<h1>Module Zeus</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table player</h2>';

$db->query("DROP TABLE IF EXISTS `player`");
$db->query("CREATE TABLE IF NOT EXISTS `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bind` varchar(50) NOT NULL,
  `rColor` int(10) unsigned NOT NULL,
  `name` varchar(25) NOT NULL,
  `avatar` varchar(25) NOT NULL,
  `status` smallint(5) unsigned NOT NULL DEFAULT '1',
  `credit` bigint(20) unsigned DEFAULT NULL,
  `uPlayer` datetime DEFAULT NULL,
  `experience` bigint(20) unsigned DEFAULT NULL,
  `factionPoint` int(11) NOT NULL,
  `level` tinyint(3) unsigned DEFAULT NULL,
  `victory` int(10) unsigned DEFAULT NULL,
  `defeat` int(10) unsigned DEFAULT NULL,
  `stepTutorial` tinyint(3) unsigned DEFAULT NULL,
  `stepDone` tinyint(1) NOT NULL,
  `iUniversity` int(10) unsigned NOT NULL DEFAULT '0',
  `partNaturalSciences` int(10) unsigned NOT NULL DEFAULT '0',
  `partLifeSciences` int(10) unsigned NOT NULL DEFAULT '0',
  `partSocialPoliticalSciences` int(10) unsigned NOT NULL DEFAULT '0',
  `partInformaticEngineering` int(10) unsigned NOT NULL DEFAULT '0',
  `dInscription` datetime DEFAULT NULL,
  `dLastConnection` datetime DEFAULT NULL,
  `dLastActivity` datetime DEFAULT NULL,
  `premium` tinyint(1) NOT NULL DEFAULT '0',
  `statement` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `fk_player_color` (`rColor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");


echo '<br /><hr />';
?>