<?php

/**
 * Sector Manager
 *
 * @author Expansion
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 20.05.13
*/
namespace Asylamba\Modules\Gaia\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Gaia\Model\Sector;

class SectorManager {
	/** @var EntityManager **/
	protected $entityManager;
	
	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param int $id
	 * @return Sector
	 */
	public function get($id) {
		return $this->entityManager->getRepository(Sector::class)->get($id);
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getFactionSectors($factionId)
	{
		return $this->entityManager->getRepository(Sector::class)->getFactionSectors($factionId);
	}
	
	/**
	 * @return array
	 */
	public function getAll()
	{
		return $this->entityManager->getRepository(Sector::class)->getAll();
	}
}