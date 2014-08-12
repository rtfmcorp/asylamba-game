<?php
echo '<h1>Module Gaia</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table changeColorPlace</h2>';

$db->query("DROP TABLE IF EXISTS `changeColorPlace`");
$db->query("CREATE TABLE IF NOT EXISTS `changeColorPlace` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rPlace` int(10) unsigned NOT NULL,
  `oldPlayer` tinyint(3) unsigned NOT NULL,
  `newPlayer` tinyint(3) unsigned NOT NULL,
  `dChangement` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_changeColorPlace_place1` (`rPlace`),
  KEY `fk_changeColorPlace_color1` (`oldPlayer`),
  KEY `fk_changeColorPlace_color2` (`newPlayer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table changeColorSector</h2>';

$db->query("DROP TABLE IF EXISTS `changeColorSector`");
$db->query("CREATE TABLE IF NOT EXISTS `changeColorSector` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rSector` int(10) unsigned NOT NULL,
  `oldColor` tinyint(3) unsigned NOT NULL,
  `newColor` tinyint(3) unsigned NOT NULL,
  `dChangement` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_changeColorSector_color1` (`oldColor`),
  KEY `fk_changeColorSector_sector1` (`rSector`),
  KEY `fk_changeColorSector_color2` (`newColor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table changeColorSystem</h2>';

$db->query("DROP TABLE IF EXISTS `changeColorSystem`");
$db->query("CREATE TABLE IF NOT EXISTS `changeColorSystem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rSystem` int(10) unsigned NOT NULL,
  `oldColor` tinyint(3) unsigned NOT NULL,
  `newColor` tinyint(3) unsigned NOT NULL,
  `dChangement` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_changeColorSystem_system1` (`rSystem`),
  KEY `fk_changeColorSystem_color1` (`oldColor`),
  KEY `fk_changeColorSystem_color2` (`newColor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table place</h2>';

$db->query("DROP TABLE IF EXISTS `place`");
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
  `uPlace` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_place_system1` (`rSystem`),
  KEY `fk_place_player1` (`rPlayer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

echo '<h3>Ajout du trigger place</h3>';

$db->query("DROP TRIGGER IF EXISTS `savePlaceChange`;");
$db->query("CREATE TRIGGER `savePlaceChange` BEFORE UPDATE ON `place`
 FOR EACH ROW BEGIN
  IF NEW.rPlayer != OLD.rPlayer THEN
  INSERT INTO changeColorPlace(
    rPlace,
    oldPlayer,
    newPlayer,
    dChangement)
  VALUES(
    OLD.id,
    OLD.rPlayer,
    NEW.rPlayer,
    NOW());
  END IF;
END;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table sector</h2>';

$db->query("DROP TABLE IF EXISTS `sector`");
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
  `name` varchar(255) DEFAULT NULL,
  `prime` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_sector_color1` (`rColor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<h3>Ajout du trigger sector</h3>';

$db->query("DROP TRIGGER IF EXISTS `saveSectorChange`;");
$db->query("CREATE TRIGGER `saveSectorChange` BEFORE UPDATE ON `sector`
 FOR EACH ROW BEGIN
  IF NEW.rColor != OLD.rColor THEN
  INSERT INTO changeColorSector(
    rSector,
    oldColor,
    newColor,
    dChangement)
  VALUES(
    OLD.id,
    OLD.rColor,
    NEW.rColor,
    NOW());
  END IF;
END;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table system</h2>';

$db->query("DROP TABLE IF EXISTS `system`");
$db->query("CREATE TABLE IF NOT EXISTS `system` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rSector` int(10) unsigned NOT NULL,
  `rColor` int(10) unsigned NOT NULL,
  `xPosition` smallint(5) unsigned DEFAULT NULL,
  `yPosition` smallint(5) unsigned DEFAULT NULL,
  `typeOfSystem` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_system_sector1` (`rSector`),
  KEY `fk_system_color1` (`rColor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

echo '<h3>Ajout du trigger system</h3>';

$db->query("DROP TRIGGER IF EXISTS `saveSystemChange`;");
$db->query("CREATE TRIGGER `saveSystemChange` BEFORE UPDATE ON `system`
 FOR EACH ROW BEGIN
  IF NEW.rColor != OLD.rColor THEN
  INSERT INTO changeColorSystem(
    rSystem,
    oldColor,
    newColor,
    dChangement)
  VALUES(
    OLD.id,
    OLD.rColor,
    NEW.rColor,
    NOW());
  END IF;
END;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout des trois vues</h2>';

$db->query("DROP VIEW IF EXISTS `vGalaxyDiary`;");
$db->query("CREATE VIEW `vGalaxyDiary` AS select `h`.`id` AS `id`,`h`.`rSector` AS `sector`,`h`.`oldColor` AS `oldColor`,`h`.`newColor` AS `newColor`,`h`.`dChangement` AS `dChangement` from (`changeColorSector` `h` left join `system` `s` on((`h`.`rSector` = `s`.`id`))) order by `h`.`dChangement` desc limit 0,100;");

$db->query("DROP VIEW IF EXISTS `vSectorDiary`;");
$db->query("CREATE VIEW `vSectorDiary` AS select count(`h`.`id`) AS `occurency`,`h`.`id` AS `id`,`h`.`rSystem` AS `system`,`s`.`rSector` AS `sector`,`h`.`oldColor` AS `oldColor`,`h`.`newColor` AS `newColor`,`h`.`dChangement` AS `dChangement` from (((`changeColorSystem` `h` left join `system` `s` on((`h`.`rSystem` = `s`.`id`))) left join `color` `c1` on((`h`.`oldColor` = `c1`.`id`))) left join `color` `c2` on((`h`.`newColor` = `c2`.`id`))) group by `c1`.`id`,`c2`.`id`,hour(`h`.`dChangement`) order by `h`.`dChangement` desc limit 0,1000;");

$db->query("DROP VIEW IF EXISTS `vSystemDiary`;");
$db->query("CREATE VIEW `vSystemDiary` AS select `h`.`id` AS `id`,`h`.`rPlace` AS `place`,`h`.`oldPlayer` AS `oldPlayer`,`p1`.`name` AS `oldName`,`c1`.`id` AS `oldColor`,`h`.`newPlayer` AS `newPlayer`,`p2`.`name` AS `newName`,`c2`.`id` AS `newColor`,`h`.`dChangement` AS `dChangement`,`p`.`position` AS `position`,`p`.`rSystem` AS `system` from (((((`changeColorPlace` `h` left join `place` `p` on((`p`.`id` = `h`.`rPlace`))) left join `player` `p1` on((`h`.`oldPlayer` = `p1`.`id`))) left join `color` `c1` on((`p1`.`rColor` = `c1`.`id`))) left join `player` `p2` on((`h`.`newPlayer` = `p2`.`id`))) left join `color` `c2` on((`p2`.`rColor` = `c2`.`id`))) order by `h`.`dChangement` desc limit 0,5000;");

echo '<br /><hr />';
?>