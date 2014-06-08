<?php
echo '<h1>Ajout de la table orbitalBaseBuildingQueue</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `orbitalBaseBuildingQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rOrbitalBase` int(11) NOT NULL,
  `buildingNumber` tinyint(3) unsigned NOT NULL,
  `targetLevel` tinyint(3) unsigned NOT NULL,
  `dStart` datetime NOT NULL,
  `dEnd` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orbitalBaseBuildingQueue_orbitalBase1` (`rOrbitalBase`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");

$qr->execute();
?>