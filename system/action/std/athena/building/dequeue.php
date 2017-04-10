<?php
# dequeue a building action

# int baseId 		id de la base orbitale
# int building 	 	id du bâtiment

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$buildingQueueManager = $this->getContainer()->get('athena.building_queue_manager');
$buildingResourceRefund = $this->getContainer()->getParameter('athena.building.building_queue_resource_refund');
$entityManager = $this->getContainer()->get('entity_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$building = $request->query->get('building');


if ($baseId !== FALSE AND $building !== FALSE AND in_array($baseId, $verif)) {
	if ($orbitalBaseHelper->isABuilding($building)) {
		if (($ob = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
			$buildingQueues = $buildingQueueManager->getBaseQueues($baseId);

			$index = NULL;
			$nbBuildingQueues = count($buildingQueues);
			for ($i = 0; $i < $nbBuildingQueues; $i++) {
				$queue = $buildingQueues[$i]; 
				# get the last element from the correct building
				if ($queue->buildingNumber == $building) {
					$index = $i;
					$targetLevel = $queue->targetLevel;
					$dStart = $queue->dStart;
				}
			}

			# if it's the first, the next must restart by now
			if ($index == 0) {
				$dStart = Utils::now();
			}

			if ($index !== NULL) {
				# shift
				for ($i = $index + 1; $i < $nbBuildingQueues; $i++) {
					$queue = $buildingQueues[$i];

					$queue->dEnd = Utils::addSecondsToDate($dStart, Utils::interval($queue->dStart, $queue->dEnd, 's'));
					$queue->dStart = $dStart;

					$dStart = $queue->dEnd;
				}

				$entityManager->remove($buildingQueues[$index]);
				$entityManager->flush(BuildingQueue::class);

				// give the resources back
				$resourcePrice = $orbitalBaseHelper->getBuildingInfo($building, 'level', $targetLevel, 'resourcePrice');
				$resourcePrice *= $buildingResourceRefund;
				$orbitalBaseManager->increaseResources($ob, $resourcePrice, TRUE);
				$session->addFlashbag('Construction annulée, vous récupérez le ' . $buildingResourceRefund * 100 . '% du montant investi pour la construction', Flashbag::TYPE_SUCCESS);
			} else {
				throw new ErrorException('suppression de bâtiment impossible');
			}
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
	} else {
		throw new ErrorException('le bâtiment indiqué n\'est pas valide');
	}
} else {
	throw new FormException('pas assez d\'informations pour annuler la construction d\'un bâtiment');
}