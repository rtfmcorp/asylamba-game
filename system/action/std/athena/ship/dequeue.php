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
use Asylamba\Modules\Athena\Model\ShipQueue;

$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$shipQueueManager = $this->getContainer()->get('athena.ship_queue_manager');
$shipResourceRefund = $this->getContainer()->getParameter('athena.building.ship_queue_resource_refund');
$scheduler = $this->getContainer()->get('realtime_action_scheduler');
$entityManager = $this->getContainer()->get('entity_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
    $verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$queue = $request->query->get('queue');
$dock = $request->query->get('dock');

if ($baseId !== false and $queue !== false and $dock !== false and in_array($baseId, $verif)) {
    if (intval($dock) > 0 and intval($dock) < 4) {
        if (($ob = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
            $shipQueues = $shipQueueManager->getByBaseAndDockType($baseId, $dock);
            $nbShipQueues = count($shipQueues);

            $index = null;
            for ($i = 0; $i < $nbShipQueues; ++$i) {
                $shipQueue = $shipQueues[$i];
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

            if ($index !== null) {
                # shift
                for ($i = $index + 1; $i < $nbShipQueues; $i++) {
                    $shipQueue = $shipQueues[$i];

                    $oldDate = $shipQueue->dEnd;
                    $shipQueue->dEnd = Utils::addSecondsToDate($dStart, Utils::interval($shipQueue->dStart, $shipQueue->dEnd, 's'));
                    $shipQueue->dStart = $dStart;

                    $scheduler->reschedule($shipQueue, $shipQueue->dEnd, $oldDate);
                    
                    $dStart = $shipQueue->dEnd;
                }

                $scheduler->cancel($shipQueues[$index], $shipQueues[$index]->dEnd);
                $entityManager->remove($shipQueues[$index]);
                $entityManager->flush(ShipQueue::class);
                // give a part of the resources back
                $resourcePrice = ShipResource::getInfo($shipNumber, 'resourcePrice');
                if ($dockType == 1) {
                    $resourcePrice *= $quantity;
                }
                $resourcePrice *= $shipResourceRefund;
                $orbitalBaseManager->increaseResources($ob, $resourcePrice, true);
                $session->addFlashbag('Commande annulée, vous récupérez le ' . $shipResourceRefund * 100 . '% du montant investi pour la construction', Flashbag::TYPE_SUCCESS);
            } else {
                throw new ErrorException('suppression de vaisseau impossible');
            }
        } else {
            throw new ErrorException('cette base ne vous appartient pas');
        }
    } else {
        throw new ErrorException('suppression de vaisseau impossible - chantier invalide');
    }
} else {
    throw new FormException('pas assez d\'informations pour enlever un vaisseau de la file d\'attente');
}
