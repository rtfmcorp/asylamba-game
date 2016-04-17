<?php
echo '<h2>Ajout de la table DA_BaseAction</h2>';

$db->query("DROP TABLE IF EXISTS `DA_BaseAction`");
$db->query("CREATE TABLE IF NOT EXISTS `DA_BaseAction` (
	`id` INT unsigned NOT NULL,
	`from` INT unsigned NOT NULL,

	`type` SMALLINT unsigned NOT NULL,
	`opt1` SMALLINT unsigned NOT NULL,
	`opt2` SMALLINT unsigned NOT NULL,
	`weight` INT unsigned NOT NULL,
	`dAction` datetime DEFAULT NULL,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");