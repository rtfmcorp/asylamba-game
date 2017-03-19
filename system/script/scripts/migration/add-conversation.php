<?php

echo '<h1>Ajout du module de Conversation</h1>';

echo '<h2>Ajout de la table Conversation</h2>';

$database = $this->getContainer()->get('database');

$qr = $database->prepare("CREATE TABLE IF NOT EXISTS `conversation` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NULL,
	`messages` INT(5) NOT NULL DEFAULT 0,
	`type` TINYINT(2) NOT NULL DEFAULT 1,
	`dCreation` datetime NOT NULL,
	`dLastMessage` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

echo '<h2>Ajout de la table userConversation</h2>';

$qr = $database->prepare("CREATE TABLE IF NOT EXISTS `conversationUser` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`rConversation` INT(11) NOT NULL,
	`rPlayer` INT(11) NOT NULL,
	`playerStatement` INT(5) NOT NULL DEFAULT 0,
	`convStatement` INT(5) NOT NULL DEFAULT 0,
	`dLastView` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

echo '<h2>Ajout de la table messageConversation</h2>';

$qr = $database->prepare("CREATE TABLE IF NOT EXISTS `conversationMessage` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`rConversation` INT(11) NOT NULL,
	`rPlayer` INT(11) NOT NULL,
	`type` INT(5) NOT NULL DEFAULT 0,

	`content` TEXT NOT NULL,

	`dCreation` datetime NOT NULL,
	`dLastModification` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();