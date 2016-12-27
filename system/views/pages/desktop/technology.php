<?php

$playerManager = $this->getContainer()->get('zeus.player_manager');
$researchManager = $this->getContainer()->get('promethee.research_manager');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');

# background paralax
echo '<div id="background-paralax" class="technology"></div>';

# inclusion des elements
# include 'technologyElement/subnav.php';
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';
	# loading des objets
	$S_PAM_TECH = $playerManager->getCurrentSession();
	$playerManager->newSession();
	$playerManager->load(array('id' => $session->get('playerId')));

	$S_RSM_TECH = $researchManager->getCurrentSession();
	$researchManager->newSession();
	$researchManager->load(array('rPlayer' => $session->get('playerId')));

	if (!$request->query->has('view') OR $request->query->get('view') == 'university') {
		$player_university = $playerManager->get(0);
		$research_university = $researchManager->get(0);
		include COMPONENT . 'tech/university.php';
	} elseif ($request->query->get('view') == 'technos') {
		include COMPONENT . 'tech/infoTech.php';
	} else {
		$this->getContainer()->redirect('404');
	}

	$researchManager->changeSession($S_RSM_TECH);
	$playerManager->changeSession($S_PAM_TECH);
echo '</div>';
