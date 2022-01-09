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
namespace App\Modules\Athena\Manager;

use App\Classes\Entity\EntityManager;

use App\Modules\Athena\Model\RecyclingLog;

class RecyclingLogManager
{
	public function __construct(protected EntityManager $entityManager)
	{
	}

	public function add(RecyclingLog $recyclingLog): void
	{
		$this->entityManager->persist($recyclingLog);
		$this->entityManager->flush($recyclingLog);
	}

	public function getBaseActiveMissionsLogs(int $baseId): array
	{
		return $this->entityManager->getRepository(RecyclingLog::class)->getBaseActiveMissionsLogs($baseId);
	}
}
