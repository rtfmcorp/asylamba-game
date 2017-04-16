<?php

/**
 * Building Queue Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 10.02.14
*/
namespace Asylamba\Modules\Athena\Manager;

use \Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Athena\Model\BuildingQueue;
use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Classes\Scheduler\RealTimeActionScheduler;

class BuildingQueueManager {
	/** @var EntityManager **/
	protected $entityManager;
	/** @var RealTimeActionScheduler **/
	protected $scheduler;
	
	/**
	 * @param EntityManager $entityManager
	 * @param RealTimeActionScheduler $scheduler
	 */
	public function __construct(EntityManager $entityManager, RealTimeActionScheduler $scheduler) {
		$this->entityManager = $entityManager;
		$this->scheduler = $scheduler;
	}
	
	/**
	 * @param int $id
	 * @return BuildingQueue
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(BuildingQueue::class)->get($id);
	}
	
	public function getBaseQueues($baseId)
	{
		return $this->entityManager->getRepository(BuildingQueue::class)->getBaseQueues($baseId);
	}
	
	/**
	 * @return array
	 */
	public function scheduleActions()
	{
		$buildingQueues = $this->entityManager->getRepository(BuildingQueue::class)->getAll();
		
		foreach ($buildingQueues as $buildingQueue) {
			$this->scheduler->schedule('athena.orbital_base_manager', 'uBuildingQueue', $buildingQueue, $buildingQueue->dEnd);
		}
	}

	/**
	 * @param BuildingQueue $buildingQueue
	 */
	public function add(BuildingQueue $buildingQueue) {
		$this->entityManager->persist($buildingQueue);
		$this->entityManager->flush($buildingQueue);
		$this->scheduler->schedule('athena.orbital_base_manager', 'uBuildingQueue', $buildingQueue, $buildingQueue->dEnd);
	}
}