<?php

$container = $this->getContainer();
$componentPath = $container->getParameter('component');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$technologyManager = $this->getContainer()->get(\App\Modules\Promethee\Manager\TechnologyManager::class);
$technologyHelper = $this->getContainer()->get(\App\Modules\Promethee\Helper\TechnologyHelper::class);
$researchHelper = $this->getContainer()->get(\App\Modules\Promethee\Helper\ResearchHelper::class);
$researchManager = $this->getContainer()->get(\App\Modules\Promethee\Manager\ResearchManager::class);
$researchQuantity = $this->getContainer()->getParameter('promethee.research.quantity');

$tech = $request->query->get('techno');
if ($technologyHelper->isATechnology($tech) AND !$technologyHelper->isATechnologyNotDisplayed($tech)) {

	$technos = $technologyManager->getPlayerTechnology($session->get('playerId'));
	$S_RSM1 = $researchManager->getCurrentSession();
	$researchManager->newSession();
	$researchManager->load(array('rPlayer' => $session->get('playerId')));
	$research = $researchManager->get();
	$researchManager->changeSession($S_RSM1);

	# toutes les infos sur la technologie :
	$name  = $technologyHelper->getInfo($tech, 'name');
	$level = $technos->getTechnology($tech);
	$image = $technologyHelper->getInfo($tech, 'imageLink'); 

	$time = $technologyHelper->getInfo($tech, 'time', $level + 1);
	$resource = $technologyHelper->getInfo($tech, 'resource', $level + 1);
	$credit = $technologyHelper->getInfo($tech, 'credit', $level + 1);
	$points = $technologyHelper->getInfo($tech, 'points', $level + 1);

	$shortDescription = $technologyHelper->getInfo($tech, 'shortDescription');
	$improvementPercentage = $technologyHelper->getImprovementPercentage($tech, $level + 1);
	$shortDescription = str_replace('{x}', $improvementPercentage, $shortDescription);

	$description = $technologyHelper->getInfo($tech, 'description');

	$technosphere = $technologyHelper->getInfo($tech, 'requiredTechnosphere');

	$requiredResearch = $technologyHelper->getInfo($tech, 'requiredResearch');

	$researchList = array();
	for ($i = 0; $i < $researchQuantity; $i++) {
		if ($requiredResearch[$i] > 0) {
			$check = TRUE;
			if ($researchManager->getResearchList($research)->get($i) < ($requiredResearch[$i] + $level)) {
				$check = FALSE;
			}
			$researchList[] = array($researchHelper->getInfo($i, 'name'), $requiredResearch[$i] + $level, $check);
		}
	}

	# component
	include $componentPath . 'tech/infoTech.php';
}
