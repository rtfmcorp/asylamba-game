<?php

/**
 * Commercial Route Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 20.05.13
*/
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Athena\Model\CommercialRoute;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Classes\Library\Game;

class CommercialRouteManager {
	/** @var EntityManager **/
	protected $entityManager;
    /** @var float **/
    protected $sectorBonus;
    /** @var float **/
    protected $factionBonus;
	
	/**
	 * @param EntityManager $entityManager
     * @param float $sectorBonus
     * @param float $factionBonus
	 */
	public function __construct(EntityManager $entityManager, $sectorBonus, $factionBonus) {
		$this->entityManager = $entityManager;
        $this->sectorBonus = $sectorBonus;
        $this->factionBonus = $factionBonus;
	}
	
	/**
	 * @param int $id
	 * @return CommercialRoute
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->get($id);
	}
	
	/**
	 * @param int $id
	 * @param int $baseId
	 * @return CommercialRoute
	 */
	public function getByIdAndBase($id, $baseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->getByIdAndBase($id, $baseId);
	}
	
	/**
	 * @param int $id
	 * @param int $baseId
	 * @return CommercialRoute
	 */
	public function getByIdAndDistantBase($id, $baseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->getByIdAndDistantBase($id, $baseId);
	}

	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getByBase($baseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->getByBase($baseId);
	}
	
	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getByDistantBase($baseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->getByDistantBase($baseId);
	}
	
	/**
	 * @param int $baseId
	 * @param int $distantBaseId
	 * @return CommercialRoute
	 */
	public function getExistingRoute($baseId, $distantBaseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->getExistingRoute($baseId, $distantBaseId);
	}
	
	/**
	 * @param OrbitalBase $orbitalBase
	 * @return int
	 */
	public function getBaseIncome(OrbitalBase $orbitalBase)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->getBaseIncome($orbitalBase->getId());
	}
	
	/**
	 * @param int $baseId
	 * @param int $distantBaseId
	 * @return bool
	 */
	public function isAlreadyARoute($baseId, $distantBaseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->isAlreadyARoute($baseId, $distantBaseId);
	}
	
	/**
	 * @param int $baseId
	 * @return int
	 */
	public function countBaseRoutes($baseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->countBaseRoutes($baseId);
	}
	
	/**
	 * @param int $baseId
	 * @return int
	 */
	public function countBaseActiveAndStandbyRoutes($baseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->countBaseActiveAndStandbyRoutes($baseId);
	}
	
	
	/**
	 * @param int $baseId
	 * @return int
	 */
	public function countBaseActiveRoutes($baseId)
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->countBaseActiveRoutes($baseId);
	}
    
    /**
     * @param OrbitalBase $base
     * @param int $playerId
     * @param int $playerFactionId
     * @param array $factions
     * @param int $min
     * @param int $max
     * @return array
     */
    public function searchRoutes(OrbitalBase $base, $playerId, $playerFactionId, $factions, $min, $max)
    {
        $results = $this
            ->entityManager
            ->getRepository(CommercialRoute::class)
            ->searchRoutes($base, $playerId, $factions, $min, $max)
        ;
        array_walk($results, function(&$route) use ($base, $playerFactionId) {
            $bonusA = ($base->getSector() != $route['rSector']) ? $this->sectorBonus : 1;
            $bonusB = ($playerFactionId) != $route['playerColor'] ? $this->factionBonus : 1;
            
            $route['price'] = Game::getRCPrice($route['distance']);
            $route['income'] = Game::getRCIncome($route['distance'], $bonusA, $bonusB);
        });
        return $results;
    }
	
	/**
	 * @param CommercialRoute $commercialRoute
	 */
	public function add(CommercialRoute $commercialRoute) {
		$this->entityManager->persist($commercialRoute);
		$this->entityManager->flush();
	}

    /**
     * @param CommercialRoute $commercialRoute
     */
    public function remove(CommercialRoute $commercialRoute)
    {
        $this->entityManager->remove($commercialRoute);
        $this->entityManager->flush();
    }
    
    public function removeBaseRoutes(OrbitalBase $orbitalBase)
    {
        $repository = $this->entityManager->getRepository(CommercialRoute::class);
        
        $routes = array_merge(
            $repository->getByBase($orbitalBase->getId()),
            $repository->getByDistantBase($orbitalBase->getId())
        );
        foreach ($routes as $route) {
            $this->entityManager->remove($route);
            // @TODO notifications
        }
        $this->entityManager->flush();
    }

    /**
     * @param Color $faction
     * @param Color $otherFaction
     */
    public function freezeRoute(Color $faction, Color $otherFaction)
    {
        $freeze = true;
        if (!($faction->colorLink[$otherFaction->id] == Color::ENEMY || $otherFaction->colorLink[$faction->id] == Color::ENEMY)) {
            $freeze = false;
        }
        $this->entityManager->getRepository(CommercialRoute::class)->freezeRoutes($faction, $otherFaction, $freeze);
    }
}
