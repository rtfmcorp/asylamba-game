<?php
include_once ATHENA;
# dequeue ship action

# int baseId 		id (rPlace) de la base orbitale
# int queue 		id de la file de construction
# int dock 			numéro du dock (1, 2, ou 3)

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$queue = Utils::getHTTPData('queue');
$dock = Utils::getHTTPData('dock');

if ($baseId !== FALSE AND $queue !== FALSE AND $dock !== FALSE AND in_array($baseId, $verif)) {
	if (intval($dock) > 0 AND intval($dock) < 4) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId));
		$ob = ASM::$obm->get();

		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession();
		ASM::$sqm->load(array('rOrbitalBase' => $baseId, 'dockType' => $dock), array('dEnd'));

		$index = NULL;
		for ($i = 0; $i < ASM::$sqm->size(); $i++) {
			$shipQueue = ASM::$sqm->get($i); 
			# get the index of the queue
			if ($shipQueue->id == $queue) {
				$index = $i;
				$dStart = $shipQueue->dStart;
				$shipNumber = $shipQueue->shipNumber;
				$dockType = $shipQueue->dockType;
				$quantity = $shipQueue->quantity;
				break;
			}
		}

		if ($index !== NULL) {
			# shift
			for ($i = $index + 1; $i < ASM::$sqm->size(); $i++) {
				$shipQueue = ASM::$sqm->get($i);

				$shipQueue->dEnd = Utils::addSecondsToDate($dStart, Utils::interval($shipQueue->dStart, $shipQueue->dEnd, 's'));
				$shipQueue->dStart = $dStart;

				$dStart = $shipQueue->dEnd;
			}

			ASM::$sqm->deleteById($queue);

			// give a part of the resources back
			$resourcePrice = ShipResource::getInfo($shipNumber, 'resourcePrice');
			if ($dockType == 1) {
				$resourcePrice *= $quantity;
			}
			$resourcePrice *= SQM_RESOURCERETURN;
			$ob->increaseResources($resourcePrice);
			CTR::$alert->add('Commande annulée, vous récupérez le ' . SQM_RESOURCERETURN * 100 . '% du montant investi pour la construction', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('suppression de vaisseau impossible', ALERT_STD_ERROR);
		}
		ASM::$sqm->changeSession($S_SQM1);
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('suppression de vaisseau impossible - chantier invalide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour enlever un vaisseau de la file d\'attente', ALERT_STD_FILLFORM);
}
?>