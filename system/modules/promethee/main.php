<?php
// config
include_once 'main.conf.php';

// classes
include_once 'class/Research.class.php';
include_once 'class/ResearchManager.class.php';
include_once 'class/Technology.class.php';
include_once 'class/TechnologyQueue.class.php';
include_once 'class/TechnologyQueueManager.class.php';

//ressources
include_once 'resource/ResearchResource.class.php';
include_once 'resource/TechnologyResource.class.php';

ASM::runPromethee();
?>
