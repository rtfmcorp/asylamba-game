<?php
// config
include_once 'main.conf.php';

// resource
include_once 'resource/SystemResource.res.php';
include_once 'resource/PlaceResource.res.php';
include_once 'resource/SquadronResource.res.php';

// classes
include_once 'class/GalaxyColorManager.class.php';

# s7/s11 - révolte seldarine --> quart supérieur droit de la carte, 2 ponts
include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v2.php';
# s8 - révolution cardanienne --> bandeau
#include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v3.php';
# s9 - empire contre-attaque --> quart de carte, 2 ponts
#include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v4.php';
# s10 - chute des archontes --> carte entière, 7 ponts
#include_once SYSTEMR . 'config/galaxies/GalaxyConfiguration.v1.php';

include_once 'class/GalaxyGenerator.class.php';
include_once 'class/PointLocation.class.php';
include_once 'class/SectorManager.class.php';
include_once 'class/Sector.class.php';
include_once 'class/Place.class.php';
include_once 'class/PlaceManager.class.php';
include_once 'class/System.class.php';
include_once 'class/SystemManager.class.php';

include_once 'class/ActionHelper.class.php';

ASM::runGaia();
?>