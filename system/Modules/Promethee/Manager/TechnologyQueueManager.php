<?php
/**
 * Technology Queue Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 10.02.14
*/
namespace Asylamba\Modules\Promethee\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\DateTimeConverter;
use Asylamba\Modules\Promethee\Message\TechnologyQueueMessage;
use Asylamba\Modules\Promethee\Model\TechnologyQueue;
use Symfony\Component\Messenger\MessageBusInterface;

class TechnologyQueueManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected MessageBusInterface $messageBus
	) {
	}
	
	public function scheduleQueues()
	{
		$queues = $this->entityManager->getRepository(TechnologyQueue::class)->getAll();

		/** @var TechnologyQueue $queue */
		foreach ($queues as $queue) {
			$this->messageBus->dispatch(
				new TechnologyQueueMessage($queue->getId()),
				[DateTimeConverter::to_delay_stamp($queue->getEndedAt())],
			);
		}
	}

	/**
	 * @param int $id
	 * @return TechnologyQueue
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(TechnologyQueue::class)->get($id);
	}
	
	/**
	 * @param int $playerId
	 * @param int $technology
	 * @return TechnologyQueue
	 */
	public function getPlayerTechnologyQueue($playerId, $technology)
	{
		return $this->entityManager->getRepository(TechnologyQueue::class)->getPlayerTechnologyQueue($playerId, $technology);
	}
	
	public function getPlaceQueues(int $placeId)
	{
		return $this->entityManager->getRepository(TechnologyQueue::class)->getPlaceQueues($placeId);
	}
	
	public function getPlayerQueues(int $playerId): array
	{
		return $this->entityManager->getRepository(TechnologyQueue::class)->getPlayerQueues($playerId);
	}

	public function add(TechnologyQueue $technologyQueue): void
	{
		$this->entityManager->persist($technologyQueue);
		$this->entityManager->flush($technologyQueue);

		$this->messageBus->dispatch(
			new TechnologyQueueMessage($technologyQueue->getId()),
			[DateTimeConverter::to_delay_stamp($technologyQueue->getEndedAt())]
		);
	}
	
	public function remove(TechnologyQueue $queue): void
	{
		// @TODO handle cancellations
		// $this->realtimeActionScheduler->cancel($queue, $queue->getEndedAt());
		
		$this->entityManager->remove($queue);
		$this->entityManager->flush($queue);
	}
}
