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

class CommercialRouteManager {
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

	// @TODO
	public function freezeRoute($color1, $color2) {
		$freeze = TRUE;
		if (!($color1->colorLink[$color2->id] == Color::ENEMY || $color2->colorLink[$color1->id] == Color::ENEMY)) {
			$freeze = FALSE;
		}
		$qr = $this->database->prepare(
			'UPDATE commercialRoute AS cr
				LEFT JOIN orbitalBase AS ob1
					ON cr.rOrbitalBase = ob1.rPlace
				LEFT JOIN player AS pl1
					ON ob1.rPlayer = pl1.id
				LEFT JOIN orbitalBase AS ob2
					ON cr.rOrbitalBaseLinked = ob2.rPlace
				LEFT JOIN player AS pl2
					ON ob2.rPlayer = pl2.id
			SET cr.statement = ?
				WHERE
					((pl1.rColor = ? AND pl2.rColor = ?) OR
					(pl1.rColor = ? AND pl2.rColor = ?)) AND
					cr.statement = ?'
		);

		if ($freeze) {
			$qr->execute(array(CommercialRoute::STANDBY, $color1->id, $color2->id, $color2->id, $color1->id, CommercialRoute::ACTIVE));
		} else {
			$qr->execute(array(CommercialRoute::ACTIVE, $color1->id, $color2->id, $color2->id, $color1->id, CommercialRoute::STANDBY));
		}
	} 
}