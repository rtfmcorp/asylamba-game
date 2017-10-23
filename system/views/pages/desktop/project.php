<?php

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$bugManager = $this->getContainer()->get('hephaistos.bug_manager');
$evolutionManager = $this->getContainer()->get('hephaistos.evolution_manager');

$bugs = $bugManager->getBugs();
$evolutions = $evolutionManager->getEvolutions();

$nbTasks = 0;
$nbProposedEvolutions = count($evolutions);
$nbAcceptedEvolutions = 0;
$nbBugs = count($bugs);

# background paralax
echo '<div id="background-paralax" class="message"></div>';

$mode = $request->query->get('mode', 'overview');

# inclusion des elements
include 'projectElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
?>
<div id="content">
    <?php include COMPONENT . 'publicity.php'; ?>
    <?php include COMPONENT . '/project/' . $mode . '.php'; ?>
</div>