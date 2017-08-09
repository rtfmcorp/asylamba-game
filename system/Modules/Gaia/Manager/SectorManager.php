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
use Asylamba\Classes\Redis\RedisManager;
use Asylamba\Classes\Process\LoadBalancer;
use Asylamba\Classes\Task\TaskManager;

use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Gaia\Manager\SystemManager;

use Asylamba\Modules\Gaia\Model\Sector;

class SectorManager {
	/** @var EntityManager **/
	protected $entityManager;
    /** @var RedisManager **/
    protected $redisManager;
    /** @var LoadBalancer **/
    protected $loadBalancer;
    /** @var TaskManager **/
    protected $taskManager;
    /** @var OrbitalBaseManager **/
    protected $orbitalBaseManager;
    /** @var PlayerManager **/
    protected $playerManager;
    /** @var SystemManager **/
    protected $systemManager;
    /** @var array **/
    protected $scores = [];
	
	/**
	 * @param EntityManager $entityManager
     * @param RedisManager $redisManager
     * @param LoadBalancer $loadBalancer
     * @param TaskManager $taskManager
     * @param OrbitalBaseManager $orbitalBaseManager
     * @param PlayerManager $playerManager
     * @param SystemManager $systemManager
     * @param array $scores
	 */
	public function __construct(
        EntityManager $entityManager,
        RedisManager $redisManager,
        LoadBalancer $loadBalancer,
        TaskManager $taskManager,
        OrbitalBaseManager $orbitalBaseManager,
        PlayerManager $playerManager,
        SystemManager $systemManager,
        $scores
    )
	{
		$this->entityManager = $entityManager;
        $this->redisManager = $redisManager;
        $this->loadBalancer = $loadBalancer;
        $this->taskManager = $taskManager;
        $this->orbitalBaseManager = $orbitalBaseManager;
        $this->playerManager = $playerManager;
        $this->systemManager = $systemManager;
        $this->scores = $scores;
	}
    
    public function initOwnershipData()
    {
        $this->loadBalancer->affectTask(
            $this->taskManager->createTechnicalTask('gaia.sector_manager', 'calculateAllOwnerships')
        );
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
        \Asylamba\Classes\Daemon\Server::debug('sectors');
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