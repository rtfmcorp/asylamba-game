<?php
// config
include_once 'main.conf.php';

# s7/s11 - révolte seldarine --> quart supérieur droit de la carte, 2 ponts
#include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v2.php';
# s8 - révolution cardanienne --> bandeau
#include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v3.php';
# s9 - empire contre-attaque --> quart de carte, 2 ponts
#include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v4.php';
# s10 - chute des archontes --> carte entière, 7 ponts
#include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v1.php';
# s13 - ... --> carte entière, 8 ponts
include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v5.php';

\Asylamba\Classes\Worker\ASM::runGaia();