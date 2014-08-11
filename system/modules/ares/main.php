<?php
// inclusion de main.conf
include_once 'main.conf.php';

// inclusion des classes d'ares
include_once 'class/Commander.class.php';
include_once 'class/CommanderManager.class.php';
include_once 'class/FightController.class.php';
include_once 'class/LiveReport.class.php';
include_once 'class/Report.class.php';
include_once 'class/ReportManager.class.php';
include_once 'class/Squadron.class.php';
include_once 'class/Ship.class.php';

# ressources
include_once 'resource/CommanderResources.php';

ASM::runAres();
?>