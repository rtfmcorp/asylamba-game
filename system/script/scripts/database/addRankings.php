<?php
echo '<h1>Ajout de la table userRanking</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `playerRanking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rRanking` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `general` smallint(6) NOT NULL,
  `generalPosition` smallint(6) NOT NULL,
  `generalVariation` smallint(6) NOT NULL,
  `experience` smallint(6) NOT NULL,
  `experiencePosition` smallint(6) NOT NULL,
  `experienceVariation` smallint(6) NOT NULL,
  `victory` smallint(6) NOT NULL,
  `victoryPosition` smallint(6) NOT NULL,
  `victoryVariation` smallint(6) NOT NULL,
  `defeat` smallint(6) NOT NULL,
  `defeatPosition` smallint(6) NOT NULL,
  `defeatVariation` smallint(6) NOT NULL,
  `ratio` smallint(6) NOT NULL,
  `ratioPosition` smallint(6) NOT NULL,
  `ratioVariation` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();


echo '<h1>Ajout de la table factionRanking</h1>';

$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `factionRanking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rRanking` int(11) NOT NULL,
  `rFaction` int(11) NOT NULL,
  `general` smallint(6) NOT NULL,
  `generalPosition` smallint(6) NOT NULL,
  `generalVariation` smallint(6) NOT NULL,
  `power` smallint(6) NOT NULL,
  `powerPosition` smallint(6) NOT NULL,
  `powerVariation` smallint(6) NOT NULL,
  `domination` smallint(6) NOT NULL,
  `dominationPosition` smallint(6) NOT NULL,
  `dominationVariation` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

echo '<h1>Ajout de la table ranking</h1>';

$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `ranking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dRanking` datetime NOT NULL,
  `player` tinyint(1) NOT NULL DEFAULT '0',
  `faction` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();
?>