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
	/** @var EntityManager **/
	protected $entityManager;
	/** @var RealTimeActionScheduler **/
	protected $realtimeActionScheduler;

	/**
	 * @param EntityManager $entityManager
	 * @param RealTimeActionScheduler $realtimeActionScheduler
	 */
	public function __construct(EntityManager $entityManager, RealTimeActionScheduler $realtimeActionScheduler)
	{
		$this->entityManager = $entityManager;
		$this->realtimeActionScheduler = $realtimeActionScheduler;
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
	
	/**
	 * @param int $placeId
	 * @return array
	 */
	public function getPlaceQueues($placeId)
	{
		return $this->entityManager->getRepository(TechnologyQueue::class)->getPlaceQueues($placeId);
	}
	
	/**
	 * @param int $playerId
	 * @return array
	 */
	public function getPlayerQueues($playerId)
	{
		return $this->entityManager->getRepository(TechnologyQueue::class)->getPlayerQueues($playerId);
	}

	/**
	 * @param TechnologyQueue $technologyQueue
	 */
	public function add(TechnologyQueue $technologyQueue) {
		$this->entityManager->persist($technologyQueue);
		$this->entityManager->flush($technologyQueue);
		
		$this->realtimeActionScheduler->schedule('athena.orbital_base_manager', 'uTechnologyQueue', $technologyQueue, $technologyQueue->getEndedAt());
	}
}