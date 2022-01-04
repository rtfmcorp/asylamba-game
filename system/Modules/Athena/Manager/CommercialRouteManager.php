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

class CommercialRouteManager
{
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
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
	 * @param CommercialRoute $commercialRoute
	 */
	public function add(CommercialRoute $commercialRoute) {
		$this->entityManager->persist($commercialRoute);
		$this->entityManager->flush();
	}

	/**
	 * @param CommercialRoute $commercialRoute
	 */
	public function remove(CommercialRoute $commercialRoute) {
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
		foreach($routes as $route) {
			$this->entityManager->remove($route);
			// @TODO notifications
		}
		$this->entityManager->flush();
	}

	/**
	 * @param Color $faction
	 * @param Color $otherFaction
	 */
	public function freezeRoute(Color $faction, Color $otherFaction) {
		$freeze = TRUE;
		if (!($faction->colorLink[$otherFaction->id] == Color::ENEMY || $otherFaction->colorLink[$faction->id] == Color::ENEMY)) {
			$freeze = FALSE;
		}
		$this->entityManager->getRepository(CommercialRoute::class)->freezeRoutes($faction, $otherFaction, $freeze);
	} 
}
