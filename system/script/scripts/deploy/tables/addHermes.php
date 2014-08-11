<?php
echo '<h1>Module Demeter</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table message</h2>';

$db->query("DROP TABLE IF EXISTS `message`");
$db->query("CREATE TABLE IF NOT EXISTS `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread` int(10) unsigned DEFAULT NULL,
  `rPlayerWriter` int(10) unsigned DEFAULT NULL,
  `rPlayerReader` int(10) unsigned NOT NULL,
  `dSending` datetime NOT NULL,
  `content` text NOT NULL,
  `readed` tinyint(1) DEFAULT '0',
  `writerStatement` tinyint(1) DEFAULT '1',
  `readerStatement` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_message_player1` (`rPlayerWriter`),
  KEY `fk_message_player2` (`rPlayerReader`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table notification</h2>';

$db->query("DROP TABLE IF EXISTS `notification`");
$db->query("CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rPlayer` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text,
  `dSending` datetime NOT NULL,
  `readed` tinyint(1) DEFAULT '0',
  `archived` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_notifications_player1` (`rPlayer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table roadMap</h2>';

$db->query("DROP TABLE IF EXISTS `roadMap`");
$db->query("CREATE TABLE IF NOT EXISTS `roadMap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `oContent` text NOT NULL,
  `pContent` text NOT NULL,
  `statement` tinyint(4) NOT NULL COMMENT '0 = caché, 1 = affiché',
  `dCreation` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table radio</h2>';

$db->query("DROP TABLE IF EXISTS `radio`");
$db->query("CREATE TABLE IF NOT EXISTS `radio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rSystem` int(10) unsigned NOT NULL,
  `rPlayer` int(10) unsigned NOT NULL,
  `oContent` text NOT NULL,
  `pContent` text NOT NULL,
  `dCreation` datetime NOT NULL,
  `statement` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 = non-validé, 1 = ok, 2 = masqué',
  PRIMARY KEY (`id`),
  KEY `rSystem` (`rSystem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

echo '<br /><hr />';
?>