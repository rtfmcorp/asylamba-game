<?php
# dequeue ship action

# int baseId 		id (rPlace) de la base orbitale
# int queue 		id de la file de construction
# int dock 			numéro du dock (1, 2, ou 3)

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Modules\Athena\Resource\ShipResource;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$shipQueueManager = $this->getContainer()->get('athena.ship_queue_manager');
$shipResourceRefund = $this->getContainer()->getParameter('athena.building.ship_queue_resource_refund');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$queue = $request->query->get('queue');
$dock = $request->query->get('dock');

if ($baseId !== FALSE AND $queue !== FALSE AND $dock !== FALSE AND in_array($baseId, $verif)) {
	if (intval($dock) > 0 AND intval($dock) < 4) {
		$S_OBM1 = $orbitalBaseManager->getCurrentSession();
		$orbitalBaseManager->newSession(ASM_UMODE);
		$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $session->get('playerId')));

		if ($orbitalBaseManager->size() > 0) {
			$ob = $orbitalBaseManager->get();

			$S_SQM1 = $shipQueueManager->getCurrentSession();
			$shipQueueManager->newSession();
			$shipQueueManager->load(array('rOrbitalBase' => $baseId, 'dockType' => $dock), array('dEnd'));

			$index = NULL;
			for ($i = 0; $i < $shipQueueManager->size(); $i++) {
				$shipQueue = $shipQueueManager->get($i); 
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

			# if it's the first, the next must restart by now
			if ($index == 0) {
				$dStart = Utils::now();
			}

			if ($index !== NULL) {
				# shift
				for ($i = $index + 1; $i < $shipQueueManager->size(); $i++) {
					$shipQueue = $shipQueueManager->get($i);

					$shipQueue->dEnd = Utils::addSecondsToDate($dStart, Utils::interval($shipQueue->dStart, $shipQueue->dEnd, 's'));
					$shipQueue->dStart = $dStart;

					$dStart = $shipQueue->dEnd;
				}

				$shipQueueManager->deleteById($queue);

				// give a part of the resources back
				$resourcePrice = ShipResource::getInfo($shipNumber, 'resourcePrice');
				if ($dockType == 1) {
					$resourcePrice *= $quantity;
				}
				$resourcePrice *= $shipResourceRefund;
				$orbitalBaseManager->increaseResources($ob, $resourcePrice, TRUE);
				$session->addFlashbag('Commande annulée, vous récupérez le ' . $shipResourceRefund * 100 . '% du montant investi pour la construction', Flashbag::TYPE_SUCCESS);
			} else {
				throw new ErrorException('suppression de vaisseau impossible');
			}
			$shipQueueManager->changeSession($S_SQM1);
		} else {
			throw new ErrorException('cette base ne vous appartient pas');	
		}
		$orbitalBaseManager->changeSession($S_OBM1);
	} else {
		throw new ErrorException('suppression de vaisseau impossible - chantier invalide');
	}
} else {
	throw new FormException('pas assez d\'informations pour enlever un vaisseau de la file d\'attente');
}