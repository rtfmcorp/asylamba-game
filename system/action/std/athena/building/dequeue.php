<?php
include_once ATHENA;
# dequeue a building action

# int baseId 		id de la base orbitale
# int building 	 	id du bâtiment

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$building = Utils::getHTTPData('building');


if ($baseId !== FALSE AND $building !== FALSE AND in_array($baseId, $verif)) {
	if (OrbitalBaseResource::isABuilding($building)) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

		if (ASM::$obm->size() > 0) {

			$ob = ASM::$obm->get();

			$S_BQM1 = ASM::$bqm->getCurrentSession();
			ASM::$bqm->newSession();
			ASM::$bqm->load(array('rOrbitalBase' => $baseId), array('dEnd'));

			$index = NULL;
			for ($i = 0; $i < ASM::$bqm->size(); $i++) {
				$queue = ASM::$bqm->get($i); 
				# get the last element from the correct building
				if ($queue->buildingNumber == $building) {
					$index = $i;
					$targetLevel = $queue->targetLevel;
					$dStart = $queue->dStart;
					$idToRemove = $queue->id;
				}
			}

			if ($index !== NULL) {
				# shift
				for ($i = $index + 1; $i < ASM::$bqm->size(); $i++) {
					$queue = ASM::$bqm->get($i);

					$queue->dEnd = Utils::addSecondsToDate($dStart, Utils::interval($queue->dStart, $queue->dEnd, 's'));
					$queue->dStart = $dStart;

					$dStart = $queue->dEnd;
				}

				ASM::$bqm->deleteById($idToRemove);

				// give the resources back
				$resourcePrice = OrbitalBaseResource::getBuildingInfo($building, 'level', $targetLevel, 'resourcePrice');
				$resourcePrice *= BQM_RESOURCERETURN;
				$ob->increaseResources($resourcePrice, TRUE);
				CTR::$alert->add('Construction annulée, vous récupérez le ' . BQM_RESOURCERETURN * 100 . '% du montant investi pour la construction', ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('suppression de bâtiment impossible', ALERT_STD_ERROR);
			}
			ASM::$bqm->changeSession($S_BQM1);
		} else {
			CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('le bâtiment indiqué n\'est pas valide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour annuler la construction d\'un bâtiment', ALERT_STD_FILLFORM);
}
?>