<?php

/**
 * RecyclingMissionManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @version 09.02.15
 **/
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Scheduler\RealTimeActionScheduler;

use Asylamba\Modules\Athena\Model\RecyclingMission;

class RecyclingMissionManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected RealTimeActionScheduler $realtimeActionScheduler
	) {
	}
	
	public function scheduleMissions(): void
	{
		$missions = $this->entityManager->getRepository(RecyclingMission::class)->getAll();
		
		foreach ($missions as $mission) {
			$this->realtimeActionScheduler->schedule(
				'athena.orbital_base_manager',
				'uRecycling',
				$mission,
				$mission->uRecycling
			);
		}
	}

	public function get(int $id): ?RecyclingMission
	{
		return $this->entityManager->getRepository(RecyclingMission::class)->get($id);
	}

	public function getBaseMissions($baseId): array
	{
		return $this->entityManager->getRepository(RecyclingMission::class)->getBaseMissions($baseId);
	}

	public function getBaseActiveMissions(int $baseId): array
	{
		return $this->entityManager->getRepository(RecyclingMission::class)->getBaseActiveMissions($baseId);
	}

	public function add(RecyclingMission $recyclingMission): void
	{
		$this->entityManager->persist($recyclingMission);
		$this->entityManager->flush($recyclingMission);
		
		$this->realtimeActionScheduler->schedule(
			'athena.orbital_base_manager',
			'uRecycling',
			$recyclingMission,
			$recyclingMission->uRecycling
		);
	}

	public function removeBaseMissions(int $baseId): void
	{
		foreach ($this->getBaseActiveMissions($baseId) as $mission) {
			$this->realtimeActionScheduler->cancel($mission, $mission->uRecycling);
		}
		$this->entityManager->getRepository(RecyclingMission::class)->removeBaseMissions($baseId);
	}
}
