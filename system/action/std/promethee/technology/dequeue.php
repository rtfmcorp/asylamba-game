<?php
# dequeue a technology action

# int baseid 		id de la base orbitale
# int techno 	 	id de la technologie

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Modules\Promethee\Model\TechnologyQueue;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$technologyHelper = $this->getContainer()->get('promethee.technology_helper');
$technologyQueueManager = $this->getContainer()->get('promethee.technology_queue_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$technologyResourceRefund = $this->getContainer()->getParameter('promethee.technology_queue.resource_refund');
$technologyCreditRefund = $this->getContainer()->getParameter('promethee.technology_queue.credit_refund');
$scheduler = $this->getContainer()->get('realtime_action_scheduler');
$entityManager = $this->getContainer()->get('entity_manager');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$techno = $request->query->get('techno');


if ($baseId !== FALSE AND $techno !== FALSE AND in_array($baseId, $verif)) {
	if ($technologyHelper->isATechnology($techno)) {
		if (($ob = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
			$placeTechnologyQueues = $technologyQueueManager->getPlaceQueues($baseId);

			$player = $playerManager->get($session->get('playerId'));

			$index = NULL;
			$targetLevel = 0;
			$nbQueues = count($placeTechnologyQueues);
			for ($i = 0; $i < $nbQueues; $i++) {
				$queue = $placeTechnologyQueues[$i];
				// get the queue to delete
				if ($queue->technology == $techno AND $queue->targetLevel > $targetLevel) {
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
				for ($i = $index + 1; $i < $nbQueues; $i++) {
					$queue = $placeTechnologyQueues[$i];

					$oldDate = $queue->dEnd;
					$queue->dEnd = Utils::addSecondsToDate($dStart, Utils::interval($queue->dStart, $queue->dEnd, 's'));
					$queue->dStart = $dStart;
					$scheduler->reschedule($queue, $queue->dEnd, $oldDate);

					$dStart = $queue->dEnd;
				}

				$scheduler->cancel($placeTechnologyQueues[$index], $placeTechnologyQueues[$index]->getEndedAt());
				$entityManager->remove($placeTechnologyQueues[$index]);
				$entityManager->flush(TechnologyQueue::class);

				// rends les ressources et les crédits au joueur
				$resourcePrice = $technologyHelper->getInfo($techno, 'resource', $targetLevel);
				$resourcePrice *= $technologyResourceRefund;
				$orbitalBaseManager->increaseResources($ob, $resourcePrice, TRUE);
				$creditPrice = $technologyHelper->getInfo($techno, 'credit', $targetLevel);
				$creditPrice *= $technologyCreditRefund;
				$playerManager->increaseCredit($player, $creditPrice);
				$session->addFlashbag('Construction annulée, vous récupérez le ' . $technologyResourceRefund * 100 . '% des ressources ainsi que le ' . $technologyCreditRefund * 100 . '% des crédits investis pour le développement', Flashbag::TYPE_SUCCESS);

			} else {
				throw new ErrorException('impossible d\'annuler la technologie');
			}
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
	} else {
		throw new ErrorException('la technologie indiquée n\'est pas valide');
	}
} else {
	throw new FormException('pas assez d\'informations pour annuler le développement d\'une technologie');
}