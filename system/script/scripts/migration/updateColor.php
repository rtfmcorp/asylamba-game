<?php

use Asylamba\Classes\Database\DatabaseAdmin;
use Asylamba\Classes\Worker\ASM;

$db = DatabaseAdmin::getInstance();

echo '<h2>Ajout de isInGame dans color</h2>';

$db->query("ALTER TABLE `color` ADD `isInGame` TINYINT NULL DEFAULT 0 AFTER `isClosed`;");

ASM::$clm->newSession();
ASM::$clm->load();

for ($i = 1; $i <= 7; $i++) {
	ASM::$clm->get($i)->isInGame = 1;
}
