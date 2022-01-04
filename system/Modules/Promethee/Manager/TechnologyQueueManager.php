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
use Asylamba\Modules\Promethee\Model\TechnologyQueue;
use Asylamba\Classes\Scheduler\RealTimeActionScheduler;

class TechnologyQueueManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected RealTimeActionScheduler $realtimeActionScheduler
	) {
	}
	
	public function scheduleQueues()
	{
		$queues = $this->entityManager->getRepository(TechnologyQueue::class)->getAll();
		
		foreach ($queues as $queue) {
			$this->realtimeActionScheduler->schedule('athena.orbital_base_manager', 'uTechnologyQueue', $queue, $queue->getEndedAt());
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
		
		$this->realtimeActionScheduler->schedule('athena.orbital_base_manager', 'uTechnologyQueue', $technologyQueue, $technologyQueue->getEndedAt());
	}
	
	public function remove(TechnologyQueue $queue): void
	{
		$this->realtimeActionScheduler->cancel($queue, $queue->getEndedAt());
		
		$this->entityManager->remove($queue);
		$this->entityManager->flush($queue);
	}
}
