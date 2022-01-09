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
namespace App\Modules\Gaia\Manager;

use App\Classes\Entity\EntityManager;
use App\Classes\Redis\RedisManager;

use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Gaia\Manager\SystemManager;

use App\Modules\Gaia\Model\Sector;

class SectorManager
{
	public function __construct(
        protected EntityManager $entityManager,
        protected RedisManager $redisManager,
        protected OrbitalBaseManager $orbitalBaseManager,
        protected PlayerManager $playerManager,
        protected SystemManager $systemManager,
        protected array $scores = [],
    ) {
	}
    
    public function initOwnershipData()
    {
        //$this->loadBalancer->affectTask(
        //    $this->taskManager->createTechnicalTask('gaia.sector_manager', 'calculateAllOwnerships')
        //);
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
	
	/**
	 * @param Sector $sector
	 */
	public function changeOwnership(Sector $sector)
	{
		$this->entityManager->getRepository(Sector::class)->changeOwnership($sector);
	}
    
    public function calculateAllOwnerships()
    {
        foreach ($this->getAll() as $sector) {
            $this->calculateOwnership($sector);
        }
    }
    
    /**
     * @param Sector $sector
     * @return array
     */
    public function calculateOwnership(Sector $sector)
    {
		$systems = $this->systemManager->getSectorSystems($sector->getId());
		$bases = $this->orbitalBaseManager->getSectorBases($sector->getId());
		$scores = [];
		
		foreach ($bases as $base)
		{
			$player = $this->playerManager->get($base->rPlayer);
			
			$scores[$player->rColor] =
				(!empty($scores[$player->rColor]))
				? $scores[$player->rColor] + $this->scores[$base->typeOfBase]
				: $this->scores[$base->typeOfBase]
			;
		}
		// For each system, the owning faction gains two points
		foreach ($systems as $system) {
			if ($system->rColor === 0) {
				continue;
			}
			$scores[$system->rColor] = (!empty($scores[$system->rColor])) ? $scores[$system->rColor] + 2 : 2;
		}
		$scores[0] = 0;
		arsort($scores);
		reset($scores);
        
        $this->redisManager->getConnection()->set('sector:' . $sector->getId(), serialize($scores));
        
        return $scores;
    }
}
