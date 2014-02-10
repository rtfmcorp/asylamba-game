<?php
echo '<h1>Ajout de la table orbitalBase</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `orbitalBase` (
  `rPlace` int(10) unsigned NOT NULL,
  `rPlayer` int(10) unsigned NOT NULL,
  `name` varchar(45) COLLATE utf8_bin NOT NULL,
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
  `iUniversity` int(10) unsigned DEFAULT '0',
  `partNaturalSciences` int(10) unsigned DEFAULT '0',
  `partLifeSciences` int(10) unsigned DEFAULT '0',
  `partSocialPoliticalSciences` int(10) unsigned DEFAULT '0',
  `partInformaticEngineering` int(10) unsigned DEFAULT '0',
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
  `isCommercialBase` tinyint(4) DEFAULT '-1',
  `isProductionRefinery` tinyint(1) DEFAULT '1',
  `isProductionDock1` tinyint(1) DEFAULT '0',
  `isProductionDock2` tinyint(1) DEFAULT '0',
  `isGravityDefense` tinyint(1) DEFAULT '1',
  `resourcesStorage` bigint(20) unsigned DEFAULT '0',
  `uResources` datetime DEFAULT NULL,
  `uAntiSpy` datetime DEFAULT NULL,
  `dCreation` datetime DEFAULT NULL,
  PRIMARY KEY (`rPlace`),
  UNIQUE KEY `id_UNIQUE` (`rPlace`),
  KEY `fk_base_player1` (`rPlayer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

$qr->execute();
?>