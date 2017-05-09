<?php

/**
 * Report Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 12.07.13
*/

namespace Asylamba\Modules\Ares\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Modules\Ares\Model\LiveReport;

class LiveReportManager
{
	/** @var EntityManager **/
	protected $entityManager;

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}
	
	/**
	 * @param int $id
	 * @return Report
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(LiveReport::class)->get($id);
	}
	
	/**
	 * @param int $playerId
	 * @return array
	 */
	public function getPlayerReports($playerId)
	{
		return $this->entityManager->getRepository(LiveReport::class)->getPlayerReports($playerId);
	}
	
	/**
	 * @param int $playerId
	 * @param array $places
	 * @return array
	 */
	public function getAttackReportsByPlaces($playerId, $places)
	{
		return $this->entityManager->getRepository(LiveReport::class)->getAttackReportsByPlaces($playerId, $places);
	}
	
	/**
	 * @param int $playerId
	 * @param boolean $hasRebels
	 * @param boolean $isArchive
	 * @return array
	 */
	public function getAttackReportsByMode($playerId, $hasRebels, $isArchive)
	{
		return $this->entityManager->getRepository(LiveReport::class)->getAttackReportsByMode($playerId, $hasRebels, $isArchive);
	}
	
	/**
	 * @param int $playerId
	 * @param boolean $hasRebels
	 * @param boolean $isArchive
	 * @return array
	 */
	public function getDefenseReportsByMode($playerId, $hasRebels, $isArchive)
	{
		return $this->entityManager->getRepository(LiveReport::class)->getDefenseReportsByMode($playerId, $hasRebels, $isArchive);
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getFactionAttackReports($factionId)
	{
		return $this->entityManager->getRepository(LiveReport::class)->getFactionAttackReports($factionId);
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getFactionDefenseReports($factionId)
	{
		return $this->entityManager->getRepository(LiveReport::class)->getFactionDefenseReports($factionId);
	}
}