<?php
echo '<h2>Ajout de la table DA_SocialRelation</h2>';

$db = $this->getContainer()->get('database');
$db->query("DROP TABLE IF EXISTS `DA_SocialRelation`");
$db->query("CREATE TABLE IF NOT EXISTS `DA_SocialRelation` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`from` INT unsigned NOT NULL,
	`to` INT unsigned NULL DEFAULT NULL,

	`type` SMALLINT unsigned NOT NULL,
	`message` TEXT NOT NULL,
	`dAction` datetime DEFAULT NULL,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");