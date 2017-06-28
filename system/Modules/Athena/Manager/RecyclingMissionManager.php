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
	
	public function scheduleMissions()
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
	
	/**
	 * @param int $id
	 * @return RecyclingMission
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(RecyclingMission::class)->get($id);
	}
	
	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getBaseMissions($baseId)
	{
		return $this->entityManager->getRepository(RecyclingMission::class)->getBaseMissions($baseId);
	}
	
	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getBaseActiveMissions($baseId)
	{
		return $this->entityManager->getRepository(RecyclingMission::class)->getBaseActiveMissions($baseId);
	}

	/**
	 * @param RecyclingMission $recyclingMission
	 */
	public function add(RecyclingMission $recyclingMission)
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
	
	/**
	 * @param int $baseId
	 */
	public function removeBaseMissions($baseId)
	{
		foreach ($this->getBaseActiveMissions($baseId) as $mission) {
			$this->realtimeActionScheduler->cancel($mission, $mission->uRecycling);
		}
		$this->entityManager->getRepository(RecyclingMission::class)->removeBaseMissions($baseId);
	}
}