<?php
// inclusion de main.conf
include_once 'main.conf.php';

// inclusion des classes d'ares
include_once 'class/Commander.class.php';
include_once 'class/CommanderManager.class.php';
include_once 'class/CommanderInFight.class.php';
include_once 'class/CommanderInFightManager.class.php';
include_once 'class/CommanderReport_v1.class.php';
include_once 'class/FightController.class.php';
include_once 'class/LiveReport.class.php';
include_once 'class/Report.class.php';
include_once 'class/Squadron.class.php';
include_once 'class/SquadronInFight.class.php';
include_once 'class/ReportManager.class.php';
//classes mère des ships
include_once 'class/ships/Ship.class.php';
include_once 'class/ships/Fighter.class.php';
include_once 'class/ships/Corvette.class.php';
include_once 'class/ships/Frigate.class.php';
include_once 'class/ships/Destroyer.class.php';
// class vaisseaux
include_once 'class/ships/Pegase.class.php';
include_once 'class/ships/Satyre.class.php';
include_once 'class/ships/Chimere.class.php';
include_once 'class/ships/Sirene.class.php';
include_once 'class/ships/Dryade.class.php';
include_once 'class/ships/Meduse.class.php';
include_once 'class/ships/Griffon.class.php';
include_once 'class/ships/Cyclope.class.php';
include_once 'class/ships/Minotaure.class.php';
include_once 'class/ships/Hydre.class.php';
include_once 'class/ships/Cerbere.class.php';
include_once 'class/ships/Phoenix.class.php';

ASM::runAres();
?>