<?php
# dequeue a technology action

# int baseid 		id de la base orbitale
# int techno 	 	id de la technologie

use App\Classes\Library\Utils;
use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;
use App\Modules\Promethee\Model\TechnologyQueue;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$orbitalBaseManager = $this->getContainer()->get(\App\Modules\Athena\Manager\OrbitalBaseManager::class);
$technologyHelper = $this->getContainer()->get(\App\Modules\Promethee\Helper\TechnologyHelper::class);
$technologyQueueManager = $this->getContainer()->get(\App\Modules\Promethee\Manager\TechnologyQueueManager::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$technologyResourceRefund = $this->getContainer()->getParameter('promethee.technology_queue.resource_refund');
$technologyCreditRefund = $this->getContainer()->getParameter('promethee.technology_queue.credit_refund');
$entityManager = $this->getContainer()->get(\App\Classes\Entity\EntityManager::class);

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
					// @TODO handle rescheduling
					//$scheduler->reschedule($queue, $queue->dEnd, $oldDate);

					$dStart = $queue->dEnd;
				}

				// @TODO handle cancellation
				//$scheduler->cancel($placeTechnologyQueues[$index], $placeTechnologyQueues[$index]->getEndedAt());
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
