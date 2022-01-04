<?php

echo '<h2>Ajout de addToNextMission dans recyclingMission</h2>';

$this->getContainer()->get(\Asylamba\Classes\Database\Database::class)->query("ALTER TABLE `recyclingMission` ADD `addToNextMission` SMALLINT unsigned NOT NULL AFTER `recyclerQuantity`;");
