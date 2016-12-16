<?php

echo '<h2>Ajout de points dans sector</h2>';

$this->getContainer()->get('database_admin')->query("ALTER TABLE `sector` ADD `points` INT NOT NULL DEFAULT 1 AFTER `lifePlanet`;");