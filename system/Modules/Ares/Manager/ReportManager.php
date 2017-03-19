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

class ReportManager {
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
	public function get($id) {
		return $this->entityManager->getRepository(Report::class)->get($id);
	}
	
	/**
	 * @param int $attackerId
	 * @param int $placeId
	 * @param string $dFight
	 * @return array
	 */
	public function getByAttackerAndPlace($attackerId, $placeId, $dFight)
	{
		return $this->entityManager->getRepository(Report::class)->getByAttackerAndPlace($attackerId, $placeId, $dFight);
	}

	/**
	 * @param Report $report
	 */
	public function add(Report $report) {
		$this->entityManager->persist($report);
		$this->entityManager->flush();
	}
}