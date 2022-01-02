<?php

$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$researchManager = $this->getContainer()->get('promethee.research_manager');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');

# background paralax
echo '<div id="background-paralax" class="technology"></div>';

# inclusion des elements
# include 'technologyElement/subnav.php';
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';
	$S_RSM_TECH = $researchManager->getCurrentSession();
	$researchManager->newSession();
	$researchManager->load(array('rPlayer' => $session->get('playerId')));

	if (!$request->query->has('view') OR $request->query->get('view') == 'university') {
		$player_university = $playerManager->get($session->get('playerId'));
		$research_university = $researchManager->get(0);
		include COMPONENT . 'tech/university.php';
	} elseif ($request->query->get('view') == 'technos') {
		include COMPONENT . 'tech/infoTech.php';
	} else {
		$this->getContainer()->redirect('404');
	}

	$researchManager->changeSession($S_RSM_TECH);
echo '</div>';
