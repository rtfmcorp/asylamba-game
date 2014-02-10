<?php
include_once ATHENA;
# dequeue a building action

# int baseId 		id de la base orbitale
# int building 	 	id du bâtiment

CTR::$alert->add('Cette action doit être mise à jour !', ALERT_STD_ERROR);

/*
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
		$ob = ASM::$obm->get();

		$S_BQM1 = ASM::$bqm->getCurrentSession();
		ASM::$bqm->newSession(ASM_UMODE);
		ASM::$bqm->load(array('rOrbitalBase' => $baseId, 'buildingNumber' => $building));

		$queue = ASM::$bqm->get();
		$id = $queue->getId();
		$targetLevel = $queue->getTargetLevel();

		for ($i = 1; $i < ASM::$bqm->size(); $i++) {
			$queue = ASM::$bqm->get($i);
			if ($queue->getTargetLevel() > $targetLevel) {
				$id = $queue->getId();
				$targetLevel = $queue->getTargetLevel();
			}
		}
		ASM::$bqm->deleteById($id);

		// rends les ressources au joueur
		$resourcePrice = OrbitalBaseResource::getBuildingInfo($building, 'level', $targetLevel, 'resourcePrice');
		$resourcePrice *= BQM_RESOURCERETURN;
		$ob->increaseResources($resourcePrice);
		CTR::$alert->add('Construction annulée, vous récupérez le ' . BQM_RESOURCERETURN * 100 . '% du montant investi pour la construction', ALERT_STD_SUCCESS);

		ASM::$obm->changeSession($S_OBM1);
		ASM::$bqm->changeSession($S_BQM1);
	} else {
		CTR::$alert->add('le bâtiment indiqué n\'est pas valide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour annuler la construction d\'un bâtiment', ALERT_STD_FILLFORM);
}*/
?>