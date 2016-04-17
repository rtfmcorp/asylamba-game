<?php
echo '<h2>Ajout de la table DA_CommercialRelation</h2>';

$db->query("DROP TABLE IF EXISTS `DA_CommercialRelation`");
$db->query("CREATE TABLE IF NOT EXISTS `DA_CommercialRelation` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`from` INT unsigned NOT NULL,
	`to` INT unsigned NOT NULL,

	`type` SMALLINT unsigned NOT NULL,
	`weight` INT unsigned NOT NULL,
	`dAction` datetime DEFAULT NULL,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");