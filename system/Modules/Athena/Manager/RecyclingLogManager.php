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
