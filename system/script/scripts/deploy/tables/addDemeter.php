<?php
echo '<h1>Module Demeter</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table forumTopic</h2>';

$db->query("DROP TABLE IF EXISTS `forumTopic`");
$db->query("CREATE TABLE IF NOT EXISTS `forumTopic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `rColor` int(10) unsigned NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `rForum` int(11) NOT NULL,
  `statement` int(11) NOT NULL DEFAULT '1',
  `dCreation` datetime NOT NULL,
  `dLastMessage` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table forumMessage</h2>';

$db->query("DROP TABLE IF EXISTS `forumMessage`");
$db->query("CREATE TABLE IF NOT EXISTS `forumMessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rTopic` int(11) NOT NULL,
  `oContent` text NOT NULL,
  `pContent` text NOT NULL,
  `statement` int(11) NOT NULL DEFAULT '1',
  `dCreation` datetime NOT NULL,
  `dLastModification` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table forumLastView</h2>';

$db->query("DROP TABLE IF EXISTS `forumLastView`");
$db->query("CREATE TABLE IF NOT EXISTS `forumLastView` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rTopic` int(11) NOT NULL,
  `dView` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table candidate</h2>';

$db->query("DROP TABLE IF EXISTS `candidate`");
$db->query("CREATE TABLE IF NOT EXISTS `candidate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rElection` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `chiefChoice` tinyint(3) unsigned DEFAULT 1,
  `treasurerChoice` tinyint(3) unsigned DEFAULT 1,
  `warlordChoice` tinyint(3) unsigned DEFAULT 1,
  `ministerChoice` tinyint(3) unsigned DEFAULT 1,
  `program` text,
  `dPresentation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table election</h2>';

$db->query("DROP TABLE IF EXISTS `election`");
$db->query("CREATE TABLE IF NOT EXISTS `election` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rColor` int(11) NOT NULL,
  `dElection` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table vote</h2>';

$db->query("DROP TABLE IF EXISTS `vote`");
$db->query("CREATE TABLE IF NOT EXISTS `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rCandidate` int(11) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `rElection` int(11) NOT NULL,
  `dVotation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table color</h2>';

$db->query("DROP TABLE IF EXISTS `color`");
$db->query("CREATE TABLE IF NOT EXISTS `color` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alive` tinyint(1) NOT NULL DEFAULT '1',
  `credits` int(11) NOT NULL,
  `players` int(11) NOT NULL,
  `activePlayers` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `sectors` smallint(11) NOT NULL,
  `electionStatement` tinyint(11) NOT NULL,
  `dLastElection` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

echo '<h3>Remplissage de la table color</h3>';
$db->query("INSERT INTO `color` (`id`, `alive`, `credits`, `players`, `activePlayers`, `points`, `sectors`, `electionStatement`, `dLastElection`) VALUES
(1, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(2, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(3, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(4, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(5, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(6, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(7, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00');");

echo '<br /><hr />';
?>