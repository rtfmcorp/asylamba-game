<?php

/**
 * System Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 09.07.13
*/
namespace Asylamba\Modules\Gaia\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Gaia\Model\System;

class SystemManager
{
	public function __construct(protected EntityManager $entityManager)
	{
	}
	
	/**
	 * @param int $id
	 * @return System
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(System::class)->get($id);
	}
	
	/**
	 * @param int $sectorId
	 * @return array
	 */
	public function getSectorSystems($sectorId)
	{
		return $this->entityManager->getRepository(System::class)->getSectorSystems($sectorId);
	}
	
	/**
	 * @return array
	 */
	public function getAll()
	{
		return $this->entityManager->getRepository(System::class)->getAll();
	}
	
	/**
	 * @param System $system
	 */
	public function changeOwnership(System $system)
	{
		$this->entityManager->getRepository(System::class)->changeOwnership($system);
	}
}
