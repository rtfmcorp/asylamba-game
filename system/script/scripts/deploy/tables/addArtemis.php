<?php
echo '<h1>Module Artémis</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table spyReport</h2>';

$db->query("DROP TABLE IF EXISTS `spyReport`");
$db->query("CREATE TABLE IF NOT EXISTS `spyReport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `rPlace` int(11) NOT NULL,
  `placeColor` smallint(6) NOT NULL,
  `typeOfBase` tinyint(4) NOT NULL,
  `typeOfOrbitalBase` tinyint(4) NOT NULL,
  `placeName` varchar(255) NOT NULL,
  `points` int(11) NOT NULL,
  `rEnemy` int(11) NOT NULL,
  `enemyName` varchar(255) NOT NULL,
  `enemyAvatar` varchar(255) NOT NULL,
  `enemyLevel` int(11) NOT NULL,
  `resources` int(11) NOT NULL,
  `commanders` text NOT NULL,
  `success` smallint(6) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `dSpying` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<br /><hr />';
?>