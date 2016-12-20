<?php
# dequeue a building action

# int baseId 		id de la base orbitale
# int building 	 	id du bâtiment

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$buildingQueueManager = $this->getContainer()->get('athena.building_queue_manager');
$buildingResourceRefund = $this->getContainer()->getParameter('athena.building.building_queue_resource_refund');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$building = $request->query->get('building');


if ($baseId !== FALSE AND $building !== FALSE AND in_array($baseId, $verif)) {
	if ($orbitalBaseHelper->isABuilding($building)) {
		$S_OBM1 = $orbitalBaseManager->getCurrentSession();
		$orbitalBaseManager->newSession();
		$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $session->get('playerId')));

		if ($orbitalBaseManager->size() > 0) {

			$ob = $orbitalBaseManager->get();

			$S_BQM1 = $buildingQueueManager->getCurrentSession();
			$buildingQueueManager->newSession();
			$buildingQueueManager->load(array('rOrbitalBase' => $baseId), array('dEnd'));

			$index = NULL;
			for ($i = 0; $i < $buildingQueueManager->size(); $i++) {
				$queue = $buildingQueueManager->get($i); 
				# get the last element from the correct building
				if ($queue->buildingNumber == $building) {
					$index = $i;
					$targetLevel = $queue->targetLevel;
					$dStart = $queue->dStart;
					$idToRemove = $queue->id;
				}
			}

			# if it's the first, the next must restart by now
			if ($index == 0) {
				$dStart = Utils::now();
			}

			if ($index !== NULL) {
				# shift
				for ($i = $index + 1; $i < $buildingQueueManager->size(); $i++) {
					$queue = $buildingQueueManager->get($i);

					$queue->dEnd = Utils::addSecondsToDate($dStart, Utils::interval($queue->dStart, $queue->dEnd, 's'));
					$queue->dStart = $dStart;

					$dStart = $queue->dEnd;
				}

				$buildingQueueManager->deleteById($idToRemove);

				// give the resources back
				$resourcePrice = $orbitalBaseHelper->getBuildingInfo($building, 'level', $targetLevel, 'resourcePrice');
				$resourcePrice *= $buildingResourceRefund;
				$ob->increaseResources($resourcePrice, TRUE);
				$response->flashbag->add('Construction annulée, vous récupérez le ' . $buildingResourceRefund * 100 . '% du montant investi pour la construction', Response::FLASHBAG_SUCCESS);
			} else {
				throw new ErrorException('suppression de bâtiment impossible');
			}
			$buildingQueueManager->changeSession($S_BQM1);
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
		$orbitalBaseManager->changeSession($S_OBM1);
	} else {
		throw new ErrorException('le bâtiment indiqué n\'est pas valide');
	}
} else {
	throw new FormException('pas assez d\'informations pour annuler la construction d\'un bâtiment');
}