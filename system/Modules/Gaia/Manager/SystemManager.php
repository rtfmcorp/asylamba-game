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

class SystemManager {
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
	 * @return System
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(System::class)->get($id);
	}
}
