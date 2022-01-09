<?php
echo '<h2>Ajout de la table DA_PlayerDaily</h2>';

$db = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$db->query("DROP TABLE IF EXISTS `DA_PlayerDaily`");
$db->query("CREATE TABLE IF NOT EXISTS `DA_PlayerDaily` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`rPlayer` INT unsigned NOT NULL,
	`credit` INT unsigned NOT NULL,
	`experience` INT unsigned NOT NULL,
	`level` INT unsigned NOT NULL,
	`victory` INT unsigned NOT NULL,
	`defeat` INT unsigned NOT NULL,
	`status` SMALLINT unsigned NOT NULL,
	`resources` INT unsigned NOT NULL,
	`fleetSize` INT unsigned NOT NULL,
	`nbPlanet` INT unsigned NOT NULL,
	`planetPoints` INT unsigned NOT NULL,
	`rkGeneral` INT unsigned NOT NULL,
	`rkFighter` INT unsigned NOT NULL,
	`rkProducer` INT unsigned NOT NULL,
	`rkButcher` INT unsigned NOT NULL,
	`rkTrader` INT unsigned NOT NULL,
	`dStorage` DATETIME NOT NULL,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
