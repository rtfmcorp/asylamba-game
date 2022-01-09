<?php

echo '<h2>Ajout de points dans sector</h2>';

$this->getContainer()->get(\App\Classes\Database\DatabaseAdmin::class)->query("ALTER TABLE `sector` ADD `points` INT NOT NULL DEFAULT 1 AFTER `lifePlanet`;");
