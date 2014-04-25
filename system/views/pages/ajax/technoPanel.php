<?php
include PROMETHEE;

$tech = CTR::$get->get('techno');
if (TechnologyResource::isATechnology($tech) AND !TechnologyResource::isATechnologyNotDisplayed($tech)) {

	$technos = new Technology(CTR::$data->get('playerId'));
	$S_RSM1 = ASM::$rsm->getCurrentSession();
	ASM::$rsm->newSession();
	ASM::$rsm->load(array('rPlayer' => CTR::$data->get('playerId')));
	$research = ASM::$rsm->get();
	ASM::$rsm->changeSession($S_RSM1);

	# toutes les infos sur la technologie :
	$name  = TechnologyResource::getInfo($tech, 'name');
	$level = $technos->getTechnology($tech);
	$image = TechnologyResource::getInfo($tech, 'imageLink'); 

	$time = TechnologyResource::getInfo($tech, 'time', $level + 1);
	$resource = TechnologyResource::getInfo($tech, 'resource', $level + 1);
	$credit = TechnologyResource::getInfo($tech, 'credit', $level + 1);
	$points = TechnologyResource::getInfo($tech, 'points', $level + 1);

	$shortDescription = TechnologyResource::getInfo($tech, 'shortDescription');
	$description = TechnologyResource::getInfo($tech, 'description');

	$technosphere = TechnologyResource::getInfo($tech, 'requiredTechnosphere');

	$requiredResearch = TechnologyResource::getInfo($tech, 'requiredResearch');

	$researchList = array();
	for ($i = 0; $i < RSM_RESEARCHQUANTITY; $i++) {
		if ($requiredResearch[$i] > 0) {
			$check = TRUE;
			if ($research->getResearchList()->get($i) < ($requiredResearch[$i] + $level)) {
				$check = FALSE;
			}
			$researchList[] = array(ResearchResource::getInfo($i, 'name'), $requiredResearch[$i] + $level, $check);
		}
	}

	# component
	include COMPONENT . 'tech/infoTech.php';
}
?>