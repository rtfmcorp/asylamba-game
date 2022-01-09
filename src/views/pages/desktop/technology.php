<?php

$container = $this->getContainer();
$componentPath = $container->getParameter('component');
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$researchManager = $this->getContainer()->get(\Asylamba\Modules\Promethee\Manager\ResearchManager::class);
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

# background paralax
echo '<div id="background-paralax" class="technology"></div>';

# inclusion des elements
# include 'technologyElement/subnav.php';
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include $componentPath . 'publicity.php';
	$S_RSM_TECH = $researchManager->getCurrentSession();
	$researchManager->newSession();
	$researchManager->load(array('rPlayer' => $session->get('playerId')));

	if (!$request->query->has('view') OR $request->query->get('view') == 'university') {
		$player_university = $playerManager->get($session->get('playerId'));
		$research_university = $researchManager->get(0);
		include $componentPath . 'tech/university.php';
	} elseif ($request->query->get('view') == 'technos') {
		include $componentPath . 'tech/infoTech.php';
	} else {
		$this->getContainer()->redirect('404');
	}

	$researchManager->changeSession($S_RSM_TECH);
echo '</div>';
