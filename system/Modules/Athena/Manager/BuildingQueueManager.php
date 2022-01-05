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

use Asylamba\Modules\Athena\Message\Building\BuildingQueueMessage;
use Asylamba\Modules\Athena\Model\BuildingQueue;
use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Classes\Scheduler\RealTimeActionScheduler;
use Asylamba\Modules\Gaia\Model\Place;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class BuildingQueueManager
{
	public function __construct(
		protected MessageBusInterface     $messenger,
		protected EntityManager           $entityManager,
		protected RealTimeActionScheduler $realtimeActionScheduler,
	) {
	}
	
	public function get(int $id): BuildingQueue
	{
		return $this->entityManager->getRepository(BuildingQueue::class)->get($id);
	}
	
	public function getBaseQueues(int $baseId): array
	{
		return $this->entityManager->getRepository(BuildingQueue::class)->getBaseQueues($baseId);
	}
	
	public function scheduleActions(): void
	{
		$buildingQueues = $this->entityManager->getRepository(BuildingQueue::class)->getAll();
		
		foreach ($buildingQueues as $buildingQueue) {
			$this->messenger->dispatch(new BuildingQueueMessage($buildingQueue->id), [new DelayStamp(1000)]);
//			$this->realtimeActionScheduler->schedule(
//				'athena.orbital_base_manager',
//				'uBuildingQueue',
//				$buildingQueue,
//				$buildingQueue->dEnd,
//				[
//					'class' => Place::class,
//					'id' => $buildingQueue->rOrbitalBase
//				]
//			);
		}
	}

	public function add(BuildingQueue $buildingQueue): void
	{
		$this->entityManager->persist($buildingQueue);
		$this->entityManager->flush($buildingQueue);
		$this->messenger->dispatch(new BuildingQueueMessage($buildingQueue->id), [new DelayStamp(1000)]);
		$this->realtimeActionScheduler->schedule(
			'athena.orbital_base_manager',
			'uBuildingQueue',
			$buildingQueue,
			$buildingQueue->dEnd,
			[
				'class' => Place::class,
				'id' => $buildingQueue->rOrbitalBase
			]
		);
	}
}
