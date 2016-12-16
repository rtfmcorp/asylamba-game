<?php

use Asylamba\Classes\Worker\ASM;

echo '<h2>Ajout de isInGame dans color</h2>';

$this->getContainer()->get('database_admin')->query("ALTER TABLE `color` ADD `isInGame` TINYINT NULL DEFAULT 0 AFTER `isClosed`;");

$colorManager = $this->getContainer()->get('demeter.color_manager');

$colorManager->newSession();
$colorManager->load();

for ($i = 1; $i <= 7; $i++) {
	$colorManager->get($i)->isInGame = 1;
}
