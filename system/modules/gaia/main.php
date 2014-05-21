<?php
// config
include_once 'main.conf.php';

// resource
include_once 'resource/SystemResource.res.php';
include_once 'resource/PlaceResource.res.php';
include_once 'resource/SquadronResource.res.php';

// classes
include_once 'class/GalaxyColorManager.class.php';
include_once 'class/GalaxyConfiguration.class.php';
include_once 'class/GalaxyGenerator.class.php';
# include_once 'class/GalaxyManager.class.php';
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