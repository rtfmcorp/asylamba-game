<?php

/**
 * Ship Queue Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 10.02.14
*/

namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Scheduler\RealTimeActionScheduler;

use Asylamba\Modules\Athena\Model\ShipQueue;

class ShipQueueManager {
	/** @var EntityManager **/
	protected $entityManager;
	/** @var RealTimeActionScheduler **/
	protected $realtimeActionScheduler;

	/**
	 * @param EntityManager $entityManager
	 * @param RealTimeActionScheduler $realtimeActionScheduler
	 */
	public function __construct(EntityManager $entityManager, RealTimeActionScheduler $realtimeActionScheduler) {
		$this->entityManager = $entityManager;
		$this->realtimeActionScheduler = $realtimeActionScheduler;
	}
	
	public function get($id)
	{
		return $this->entityManager->getRepository(ShipQueue::class)->get($id);
	}
	
	/**
	 * @param int $orbitalBaseId
	 * @return array
	 */
	public function getBaseQueues($orbitalBaseId)
	{
		return $this->entityManager->getRepository(ShipQueue::class)->getBaseQueues($orbitalBaseId);
	}
	
	/**
	 * @param int $orbitalBaseId
	 * @param int $dockType
	 * @return array
	 */
	public function getByBaseAndDockType($orbitalBaseId, $dockType)
	{
		return $this->entityManager->getRepository(ShipQueue::class)->getByBaseAndDockType($orbitalBaseId, $dockType);
	}
	
	/**
	 * @param ShipQueue $shipQueue
	 */
	public function add(ShipQueue $shipQueue)
	{
		$this->entityManager->persist($shipQueue);
		$this->entityManager->flush($shipQueue);
		
		$this->realtimeActionScheduler->schedule(
			'athena.orbital_base_manager',
			'uShipQueue' . $shipQueue->dockType,
			$shipQueue,
			$shipQueue->dEnd,
			[
				'class' => Place::class,
				'id' => $shipQueue->rOrbitalBase
			]
		);
	}
	
	public function scheduleActions()
	{
		$queues = $this->entityManager->getRepository(ShipQueue::class)->getAll();
		
		foreach ($queues as $queue)
		{
			$this->realtimeActionScheduler->schedule(
				'athena.orbital_base_manager',
				'uShipQueue' . $queue->dockType,
				$queue,
				$queue->dEnd,
				[
					'class' => Place::class,
					'id' => $queue->rOrbitalBase
				]
			);
		}
	}
}