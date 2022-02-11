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
namespace App\Modules\Athena\Manager;

use App\Classes\Entity\EntityManager;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Model\CommercialRoute;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Demeter\Model\Color;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Zeus\Model\Player;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommercialRouteManager
{
	public function __construct(
		protected RequestStack $requestStack,
		protected OrbitalBaseHelper $orbitalBaseHelper,
		protected EntityManager $entityManager
	) {
	}

	/**
	 * @return array{
	 *	waiting_for_me: int,
	 *  waiting_for_other: int,
	 *  operational: int,
	 *  stand_by: int,
	 *  total: int,
	 *  total_income: int,
	 *  max: int
	 * }
	 **/
	public function getBaseCommercialData(OrbitalBase $orbitalBase): array
	{
		$session = $this->requestStack->getSession();
		$routes = array_merge(
			$this->getByBase($orbitalBase->getId()),
			$this->getByDistantBase($orbitalBase->getId())
		);
		//if (0 === count($routes)) {
		//	return [];
		//}

		$nCRWaitingForOther = 0;
		$nCRWaitingForMe = 0;
		$nCROperational = 0;
		$nCRInStandBy = 0;
		$totalIncome = 0;

		/** @var CommercialRoute $route */
		foreach ($routes as $route) {
			if ($route->getStatement() == CommercialRoute::PROPOSED AND $route->getPlayerId1() == $session->get('playerId')) {
				$nCRWaitingForOther++;
			} elseif ($route->getStatement() == CommercialRoute::PROPOSED AND $route->getPlayerId1() != $session->get('playerId')) {
				$nCRWaitingForMe++;
			} elseif ($route->getStatement() == CommercialRoute::ACTIVE) {
				$totalIncome += $route->getIncome();
				$nCROperational++;
			} elseif ($route->getStatement() == CommercialRoute::STANDBY) {
				$nCRInStandBy++;
			}
		}

		return [
			'waiting_for_me' => $nCRWaitingForMe,
			'waiting_for_other' => $nCRWaitingForOther,
			'operational' => $nCROperational,
			'stand_by' => $nCRInStandBy,
			'total' => $nCROperational + $nCRInStandBy + $nCRWaitingForOther,
			'total_income' => $totalIncome,
			'max' => $this->orbitalBaseHelper->getBuildingInfo(
				OrbitalBaseResource::SPATIOPORT,
				'level',
				$orbitalBase->getLevelSpatioport(),
				'nbRoutesMax'
			),
		];
	}

	public function searchCandidates(int $playerId, OrbitalBase $orbitalBase, array $factions, int $minDistance, int $maxDistance): array
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->searchCandidates($playerId, $orbitalBase, $factions, $minDistance, $maxDistance);
	}

	// @TODO use an appropriate DTO for this
	public function getAllPlayerRoutes(Player $player): array
	{
		return $this->entityManager->getRepository(CommercialRoute::class)->getAllPlayerRoutes($player);
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
