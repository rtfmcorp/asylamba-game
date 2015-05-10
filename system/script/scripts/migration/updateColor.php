<?php
$db = DataBaseAdmin::getInstance();

echo '<h2>Ajout de dClaimVictory dans color</h2>';

$db->query("ALTER TABLE `color` ADD `dClaimVictory` DATETIME NULL DEFAULT NULL AFTER `isClosed`;");