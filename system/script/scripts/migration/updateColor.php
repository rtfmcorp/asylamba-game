<?php

use Asylamba\Modules\Demeter\Model\Color;

echo '<h2>Ajout de isInGame dans color</h2>';

$this->getContainer()->get('database_admin')->query("ALTER TABLE `color` ADD `isInGame` TINYINT NULL DEFAULT 0 AFTER `isClosed`;");

$factions = $this->getContainer()->get('demeter.color_manager')->getAll();

foreach ($factions as $faction) {
    $faction->isInGame = 1;
}

$this->getContainer()->get('entity_manager')->flush(Color::class);
