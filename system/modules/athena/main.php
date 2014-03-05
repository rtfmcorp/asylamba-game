<?php
// config
include_once 'main.conf.php';

// classes
include_once 'class/OrbitalBaseManager.class.php';
include_once 'class/OrbitalBase.class.php';
include_once 'class/MotherShip.class.php';

include_once 'class/CommercialRoute.class.php';
include_once 'class/CommercialRouteManager.class.php';

include_once 'class/BuildingQueue.class.php';
include_once 'class/BuildingQueueManager.class.php';

include_once 'class/ShipQueue.class.php';
include_once 'class/ShipQueueManager.class.php';

include_once 'class/Transaction.class.php';
include_once 'class/TransactionManager.class.php';

include_once 'class/CommercialShipping.class.php';
include_once 'class/CommercialShippingManager.class.php';

// ressources
include_once 'resource/MotherShipResource.class.php';
include_once 'resource/OrbitalBaseResource.class.php';
include_once 'resource/ShipResource.class.php';
include_once 'resource/SchoolClassResource.class.php';

ASM::runAthena();
?>