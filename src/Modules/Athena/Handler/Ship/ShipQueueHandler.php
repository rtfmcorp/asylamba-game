<?php

namespace App\Modules\Athena\Handler\Ship;

use App\Classes\Daemon\ClientManager;
use App\Classes\Entity\EntityManager;
use App\Classes\Library\Flashbag;
use App\Classes\Library\Format;
use App\Classes\Library\Session\SessionWrapper;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Manager\ShipQueueManager;
use App\Modules\Athena\Message\Ship\ShipQueueMessage;
use App\Modules\Athena\Model\ShipQueue;
use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Zeus\Manager\PlayerManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ShipQueueHandler implements MessageHandlerInterface
{
	public function __construct(
		protected ShipQueueManager $shipQueueManager,
		protected PlayerManager $playerManager,
		protected ClientManager $clientManager,
		protected SessionWrapper $sessionWrapper,
		protected EntityManager $entityManager,
		protected OrbitalBaseManager $orbitalBaseManager,
	) {

	}

	public function __invoke(ShipQueueMessage $message): void
	{
		$queue = $this->shipQueueManager->get($message->getShipQueueId());
		$orbitalBase = $this->orbitalBaseManager->get($queue->rOrbitalBase);
		$player = $this->playerManager->get($orbitalBase->rPlayer);
		# vaisseau construit
		$orbitalBase->setShipStorage($queue->shipNumber, $orbitalBase->getShipStorage($queue->shipNumber) + $queue->quantity);
		# increase player experience
		$experience = $queue->quantity * ShipResource::getInfo($queue->shipNumber, 'points');
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if (($session = $this->clientManager->getSessionByPlayerId($player->getId())) !== null) {
			$shipName = ShipResource::getInfo($queue->shipNumber, 'codeName');
			$session->addFlashbag(\sprintf(
				'Construction de %s</strong> sur <strong>%s</strong> terminée. Vous gagnez %s point%s d\'expérience.',
				($queue->quantity > 1)
					? \sprintf('vos <strong>%s %ss', $queue->quantity, $shipName)
					: \sprintf('votre %s<strong>', $shipName),
				$orbitalBase->name,
				$experience,
				Format::addPlural($experience),
			), 1 === $queue->dockType ? Flashbag::TYPE_DOCK1_SUCCESS : Flashbag::TYPE_DOCK2_SUCCESS);
			$this->sessionWrapper->save($session);
		}
		$this->entityManager->remove($queue);
		$this->entityManager->flush();
	}
}
