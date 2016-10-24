<?php

use Asylamba\Classes\Database\DatabaseAdmin;

$db = DatabaseAdmin::getInstance();

echo '<h2>Ajout de addToNextMission dans recyclingMission</h2>';

$db->query("ALTER TABLE `recyclingMission` ADD `addToNextMission` SMALLINT unsigned NOT NULL AFTER `recyclerQuantity`;");
