<?php

namespace App\Modules\Athena\Handler\Building;

use App\Classes\Daemon\ClientManager;
use App\Classes\Entity\EntityManager;
use App\Classes\Library\Flashbag;
use App\Classes\Library\Format;
use App\Classes\Library\Session\SessionWrapper;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Manager\BuildingQueueManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Message\Building\BuildingQueueMessage;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Zeus\Manager\PlayerManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BuildingQueueHandler implements MessageHandlerInterface
{
	public function __construct(
		protected BuildingQueueManager $buildingQueueManager,
		protected PlayerManager $playerManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected OrbitalBaseHelper $orbitalBaseHelper,
		protected EntityManager $entityManager,
		protected SessionWrapper $sessionWrapper,
		protected ClientManager $clientManager,
		protected LoggerInterface $logger,
	) {
	}

	public function __invoke(BuildingQueueMessage $message)
	{
		$this->logger->info('Handle building completion for queue {queueId}', [
			'queueId' => $message->getBuildingQueueId(),
		]);
		$queue = $this->buildingQueueManager->get($message->getBuildingQueueId());
		$orbitalBase = $this->orbitalBaseManager->get($queue->rOrbitalBase);
		$player = $this->playerManager->get($orbitalBase->rPlayer);
		# update builded building
		$orbitalBase->setBuildingLevel($queue->buildingNumber, ($orbitalBase->getBuildingLevel($queue->buildingNumber) + 1));
		# update the points of the orbitalBase
		$earnedPoints = $this->orbitalBaseManager->updatePoints($orbitalBase);
		$this->entityManager->getRepository(OrbitalBase::class)->increaseBuildingLevel(
			$orbitalBase,
			$this->orbitalBaseHelper->getBuildingInfo($queue->buildingNumber, 'column'),
			$earnedPoints
		);
		# increase player experience
		$experience = $this->orbitalBaseHelper->getBuildingInfo($queue->buildingNumber, 'level', $queue->targetLevel, 'points');
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if (($session = $this->clientManager->getSessionByPlayerId($player->getId())) !== null) {
			$session->addFlashbag('Construction de votre <strong>' . $this->orbitalBaseHelper->getBuildingInfo($queue->buildingNumber, 'frenchName') . ' niveau ' . $queue->targetLevel . '</strong> sur <strong>' . $orbitalBase->name . '</strong> terminée. Vous gagnez ' . $experience . ' point' . Format::addPlural($experience) . ' d\'expérience.', Flashbag::TYPE_GENERATOR_SUCCESS);
			$this->sessionWrapper->save($session);
		}
		# delete queue in database
		$this->entityManager->remove($queue);
		$this->entityManager->flush($queue);
		$this->logger->info('Construction done');
	}
}
