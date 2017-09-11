<?php
echo '<h2>Ajout de la table DA_Player</h2>';

$db = $this->getContainer()->get('database');
$db->query("DROP TABLE IF EXISTS `DA_Player`");
$db->query("CREATE TABLE IF NOT EXISTS `DA_Player` (
	`id` INT unsigned NOT NULL,
	`color` SMALLINT unsigned NOT NULL,
	`dInscription` datetime DEFAULT NULL,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
