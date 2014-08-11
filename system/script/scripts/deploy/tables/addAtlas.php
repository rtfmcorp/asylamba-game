<?php
echo '<h1>Module Atlas</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table playerRanking</h2>';

$qr = $db->prepare("DROP TABLE IF EXISTS `playerRanking`");
$qr->execute();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `playerRanking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rRanking` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `general` int(11) NOT NULL,
  `generalPosition` smallint(6) NOT NULL,
  `generalVariation` smallint(6) NOT NULL,
  `experience` int(11) NOT NULL,
  `experiencePosition` smallint(6) NOT NULL,
  `experienceVariation` smallint(6) NOT NULL,
  `victory` int(11) NOT NULL,
  `victoryPosition` smallint(6) NOT NULL,
  `victoryVariation` smallint(6) NOT NULL,
  `defeat` int(11) NOT NULL,
  `defeatPosition` smallint(6) NOT NULL,
  `defeatVariation` smallint(6) NOT NULL,
  `ratio` int(11) NOT NULL,
  `ratioPosition` smallint(6) NOT NULL,
  `ratioVariation` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table factionRanking</h2>';

$qr = $db->prepare("DROP TABLE IF EXISTS `factionRanking`");
$qr->execute();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `factionRanking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rRanking` int(11) NOT NULL,
  `rFaction` int(11) NOT NULL,
  `general` int(11) NOT NULL,
  `generalPosition` smallint(6) NOT NULL,
  `generalVariation` smallint(6) NOT NULL,
  `power` int(11) NOT NULL,
  `powerPosition` smallint(6) NOT NULL,
  `powerVariation` smallint(6) NOT NULL,
  `domination` int(11) NOT NULL,
  `dominationPosition` smallint(6) NOT NULL,
  `dominationVariation` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table ranking</h2>';

$qr = $db->prepare("DROP TABLE IF EXISTS `ranking`");
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `ranking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dRanking` datetime NOT NULL,
  `player` tinyint(1) NOT NULL DEFAULT '0',
  `faction` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

echo '<br /><hr />';
?>