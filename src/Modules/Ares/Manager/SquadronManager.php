<?php

namespace App\Modules\Ares\Manager;

use App\Classes\Entity\EntityManager;

use App\Modules\Ares\Model\Commander;
use App\Modules\Ares\Model\Squadron;

class SquadronManager
{
	public function __construct(protected EntityManager $entityManager)
	{
	}
	
	/**
	 * @param Commander $commander
	 * @return array
	 */
	public function getCommanderSquadrons(Commander $commander)
	{
		return $this->entityManager->getRepository(Squadron::class)->getCommanderSquadrons($commander->getId());
	}
	
	/**
	 * @param int $id
	 * @return Squadron
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(Squadron::class)->get($id);
	}
}
