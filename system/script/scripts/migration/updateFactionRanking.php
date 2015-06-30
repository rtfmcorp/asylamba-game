<?php
$db = DataBaseAdmin::getInstance();

echo '<h2>Ajout du classement total (points) dans playerRanking</h2>';

$db->query("ALTER TABLE `factionRanking` ADD `newPoints` SMALLINT NOT NULL AFTER `rFaction`;");
$db->query("ALTER TABLE `factionRanking` ADD `pointsVariation` SMALLINT NOT NULL AFTER `rFaction`;");
$db->query("ALTER TABLE `factionRanking` ADD `pointsPosition` SMALLINT NOT NULL AFTER `rFaction`;");
$db->query("ALTER TABLE `factionRanking` ADD `points` INT unsigned NOT NULL AFTER `rFaction`;");

echo '<h2>Ajout des points du classement total dans color</h2>';

$db->query("ALTER TABLE `color` ADD `rankingPoints` INT unsigned NOT NULL DEFAULT 0 AFTER `activePlayers`;");
