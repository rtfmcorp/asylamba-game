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
use Asylamba\Modules\Gaia\Model\Place;

class ShipQueueManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected RealTimeActionScheduler $realtimeActionScheduler
	) {
	}
	
	public function get($id)
	{
		return $this->entityManager->getRepository(ShipQueue::class)->get($id);
	}

	public function getBaseQueues(int $orbitalBaseId): array
	{
		return $this->entityManager->getRepository(ShipQueue::class)->getBaseQueues($orbitalBaseId);
	}

	public function getByBaseAndDockType(int $orbitalBaseId, int $dockType): array
	{
		return $this->entityManager->getRepository(ShipQueue::class)->getByBaseAndDockType($orbitalBaseId, $dockType);
	}

	public function add(ShipQueue $shipQueue): void
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
	
	public function scheduleActions(): void
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
