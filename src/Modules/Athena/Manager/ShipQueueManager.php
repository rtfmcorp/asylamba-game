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

namespace App\Modules\Athena\Manager;

use App\Classes\Entity\EntityManager;

use App\Classes\Library\DateTimeConverter;
use App\Modules\Athena\Message\Ship\ShipQueueMessage;
use App\Modules\Athena\Model\ShipQueue;
use Symfony\Component\Messenger\MessageBusInterface;

class ShipQueueManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected MessageBusInterface $messageBus,
	) {
	}
	
	public function get($id): ?ShipQueue
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

		$this->messageBus->dispatch(new ShipQueueMessage($shipQueue->getId()), [DateTimeConverter::to_delay_stamp($shipQueue->dEnd)]);
	}
	
	public function scheduleActions(): void
	{
		$queues = $this->entityManager->getRepository(ShipQueue::class)->getAll();
		
		foreach ($queues as $queue)
		{
			$this->messageBus->dispatch(new ShipQueueMessage($queue->getId()), [DateTimeConverter::to_delay_stamp($queue->dEnd)]);
		}
	}
}
