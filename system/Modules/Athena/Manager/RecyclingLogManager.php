<?php

/**
 * RecyclingLogManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @version 09.02.15
 **/
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Athena\Model\RecyclingLog;

class RecyclingLogManager {
	/** @var EntityManager **/
	protected $entityManager;

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * @param RecyclingLog $recyclingLog
	 */
	public function add(RecyclingLog $recyclingLog)
	{
		$this->entityManager->persist($recyclingLog);
		$this->entityManager->flush($recyclingLog);
	}
}