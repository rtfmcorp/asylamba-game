<?php
echo '<h1>Ajout de la table player</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bind` varchar(50) COLLATE utf8_bin NOT NULL,
  `rColor` int(10) unsigned NOT NULL,
  `name` varchar(25) COLLATE utf8_bin NOT NULL,
  `avatar` varchar(25) COLLATE utf8_bin NOT NULL,
  `status` smallint(5) unsigned NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_bin NOT NULL,
  `credit` bigint(20) unsigned DEFAULT NULL,
  `uPlayer` datetime DEFAULT NULL,
  `experience` bigint(20) unsigned DEFAULT NULL,
  `level` tinyint(3) unsigned DEFAULT NULL,
  `victory` int(10) unsigned DEFAULT NULL,
  `defeat` int(10) unsigned DEFAULT NULL,
  `stepTutorial` tinyint(3) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");

$qr->execute();
?>