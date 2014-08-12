<?php

# call all the module scripts
include SCRIPT . 'scripts/deploy/tables/addZeus.php';
include SCRIPT . 'scripts/deploy/tables/addApollon.php';
include SCRIPT . 'scripts/deploy/tables/addAres.php';
include SCRIPT . 'scripts/deploy/tables/addArtemis.php';
include SCRIPT . 'scripts/deploy/tables/addAthena.php';
include SCRIPT . 'scripts/deploy/tables/addAtlas.php';
include SCRIPT . 'scripts/deploy/tables/addDemeter.php';
include SCRIPT . 'scripts/deploy/tables/addHermes.php';
include SCRIPT . 'scripts/deploy/tables/addPromethee.php';
include SCRIPT . 'scripts/deploy/tables/addGaia.php';


echo '<h1>Génération de la galaxie</h1>';

include_once GAIA;
GalaxyGenerator::generate();
echo GalaxyGenerator::getLog();

?>