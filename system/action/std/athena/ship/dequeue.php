<?php
include_once ATHENA;
# dequeue ship action

# int baseId 		id (rPlace) de la base orbitale
# int queue 		id de la file de construction
# int dock 			numéro du dock (1, 2, ou 3)

CTR::$alert->add('L\'action doit être mise à jour !', ALERT_STD_ERROR);


/*for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (CTR::$get->exist('baseid')) {
	$baseId = CTR::$get->get('baseid');
} elseif (CTR::$post->exist('baseid')) {
	$baseId = CTR::$post->get('baseid');
} else {
	$baseId = FALSE;
}
if (CTR::$get->exist('queue')) {
	$queue = CTR::$get->get('queue');
} elseif (CTR::$post->exist('queue')) {
	$queue = CTR::$post->get('queue');
} else {
	$queue = FALSE;
}
if (CTR::$get->exist('dock')) {
	$dock = CTR::$get->get('dock');
} elseif (CTR::$post->exist('dock')) {
	$dock = CTR::$post->get('dock');
} else {
	$dock = FALSE;
}

if ($baseId !== FALSE AND $queue !== FALSE AND $dock !== FALSE AND in_array($baseId, $verif)) {
	if (intval($dock) > 0 AND intval($dock) < 4) {
		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession(ASM_UMODE);
		ASM::$sqm->load(array('id' => $queue, 'rOrbitalBase' => $baseId, 'dockType' => $dock));
		if (ASM::$sqm->get()) {
			$sq = ASM::$sqm->get();

			//rends une partie des ressources au joueur
			$S_OBM1 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession(ASM_UMODE);
			ASM::$obm->load(array('rPlace' => $baseId));
			$ob = ASM::$obm->get();
			$resourcePrice = ShipResource::getInfo($sq->getShipNumber(), 'resourcePrice');
			if ($sq->getDockType() == 1) {
				$resourcePrice *= $sq->getQuantity();
			}

			$resourcePrice *= SQM_RESOURCERETURN;
			
			$ob->increaseResources($resourcePrice);

			//enlève le pack de vaisseaux de la file d'attente
			ASM::$sqm->deleteById($queue);

			CTR::$alert->add('Commande annulée, vous récupérez le ' . SQM_RESOURCERETURN * 100 . '% du montant investi pour la construction', ALERT_STD_SUCCESS);
			ASM::$obm->changeSession($S_OBM1);
		} else {
			CTR::$alert->add('suppression de vaisseau impossible', ALERT_STD_ERROR);
		}
		ASM::$sqm->changeSession($S_SQM1);
	} else {
		CTR::$alert->add('suppression de vaisseau impossible - chantier invalide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour enlever un vaisseau de la file d\'attente', ALERT_STD_FILLFORM);
}*/
?>