<?php
include_once ATHENA;
include_once PROMETHEE;
# building a building action

# int baseid 		id de la base orbitale
# int building 	 	id du bâtiment

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (CTR::$get->exist('baseid')) {
	$baseId = CTR::$get->get('baseid');
} elseif (CTR::$post->exist('baseid')) {
	$baseId = CTR::$post->get('baseid');
} else {
	$baseId = FALSE;
}
if (CTR::$get->exist('building')) {
	$building = CTR::$get->get('building');
} elseif (CTR::$post->exist('building')) {
	$building = CTR::$post->get('building');
} else {
	$building = FALSE;
}

if ($baseId !== FALSE AND $building !== FALSE AND in_array($baseId, $verif)) {
	if (OrbitalBaseResource::isABuilding($building)) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));
		$ob  = ASM::$obm->get();

		$S_BQM1 = ASM::$bqm->getCurrentSession();
		ASM::$bqm->newSession(ASM_UMODE);
		ASM::$bqm->load(array('rOrbitalBase' => $baseId), array('position'));

		$currentLevel = call_user_func(array($ob, 'getReal' . ucfirst(OrbitalBaseResource::getBuildingInfo($building, 'name')) . 'Level'));
		$technos = new Technology(CTR::$data->get('playerId'));
		if (OrbitalBaseResource::haveRights($building, $currentLevel + 1, 'resource', $ob->getResourcesStorage()) 
			AND OrbitalBaseResource::haveRights($building, $currentLevel + 1, 'queue', ASM::$bqm->size()) 
			AND (OrbitalBaseResource::haveRights($building, $currentLevel + 1, 'buildingTree', $ob) === TRUE)
			AND OrbitalBaseResource::haveRights($building, $currentLevel + 1, 'techno', $technos)) {
			if (ASM::$bqm->size() > 0) {
				for ($i = 0; $i < ASM::$bqm->size(); $i++) {
					$bq = ASM::$bqm->get($i);
					$bq->setPosition($i + 1);
				}
			}
			// construit le nouveau batiment
			$bq = new BuildingQueue();
			$bq->setROrbitalBase($baseId);
			$bq->setBuildingNumber($building);
			$bq->setTargetLevel($currentLevel+1);
			$time = OrbitalBaseResource::getBuildingInfo($building, 'level', $currentLevel + 1, 'time');
			$bonus = $time * CTR::$data->get('playerBonus')->get(PlayerBonus::GENERATOR_SPEED) / 100;
			$bq->setRemainingTime(round($time - $bonus));
			$bq->setPosition(ASM::$bqm->size()+1);
			ASM::$bqm->add($bq);

			// debit resources
			$ob->decreaseResources(OrbitalBaseResource::getBuildingInfo($building, 'level', $currentLevel + 1, 'resourcePrice'));
			
			// definir si la base est commerciale ou non (si besoin)
			if ($building == 3 || $building == 6) {
				if ($ob->getRealDock2Level() == 0 && $ob->getRealCommercialPlateformeLevel() == 0) {
					$commercial = ($building == 3) ? 0 : 1;
					$ob->setIsCommercialBase($commercial);
				}
			}

			// ajout de l'event dans le contrôleur
			$date = Utils::now();
			for ($i = 0; $i < ASM::$bqm->size(); $i++) { 
				$date = Utils::addSecondsToDate($date, ASM::$bqm->get($i)->getRemainingTime());
			}
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);

			CTR::$alert->add('Construction programmée', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('les conditions ne sont pas remplies pour construire ce bâtiment', ALERT_STD_ERROR);
		}
		ASM::$bqm->changeSession($S_BQM1);
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('le bâtiment indiqué n\'est pas valide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour construire un bâtiment', ALERT_STD_FILLFORM);
}
?>
