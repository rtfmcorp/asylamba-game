<?php

namespace App\Modules\Zeus\Infrastructure\Controller;

use App\Classes\Entity\EntityManager;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Manager\BuildingQueueManager;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Manager\ShipQueueManager;
use App\Modules\Athena\Model\CommercialRoute;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Promethee\Helper\TechnologyHelper;
use App\Modules\Zeus\Manager\PlayerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProfileController extends AbstractController
{
	public function __invoke(
		Request $request,
		OrbitalBaseManager $orbitalBaseManager,
		OrbitalBaseHelper $orbitalBaseHelper,
		CommercialRouteManager $commercialRouteManager,
		BuildingQueueManager $buildingQueueManager,
		ShipQueueManager $shipQueueManager,
		PlayerManager $playerManager,
		EntityManager $entityManager,
		TechnologyHelper $technologyHelper,
	): Response {
		$session = $request->getSession();

		if (null === $session->get('playerId')) {
			return $this->redirectToRoute('homepage');
		}

		$player = $playerManager->get($session->get('playerId'));

		// @TODO All this stuff needs to go in a dedicated service which will hold the logic
		$baseLevelPlayer = $this->getParameter('zeus.player.base_level');
		$playerExperience = $player->getExperience();
		$playerMissingExperience = $baseLevelPlayer * (pow(2, ($player->getLevel() - 1)));
		// @TODO Not quite sure that this is the next experience level. To check and rename accordingly
		$playerNextLevelExperience = $baseLevelPlayer * (pow(2, ($player->getLevel() - 2)));
		$playerExperienceProgress = ((($playerExperience - $playerNextLevelExperience) * 200) / $playerMissingExperience);

		// $sessionToken = $session->get('token');

		$playerBases = $orbitalBaseManager->getPlayerBases($session->get('playerId'));

		foreach ($playerBases as $orbitalBase) {
			// @TODO: move it to the using part of the code and remove useless data
			if ($orbitalBase->getLevelSpatioport() > 0) {
				$orbitalBase->commercialRouteData = $this->getCommercialRouteNumbers($session, $orbitalBaseHelper, $commercialRouteManager, $orbitalBase);
			}

			// @TODO Move to dedicated service
			$orbitalBase->dock1ShipQueues = $shipQueueManager->getByBaseAndDockType($orbitalBase->rPlace, 1);
			$orbitalBase->dock2ShipQueues = $shipQueueManager->getByBaseAndDockType($orbitalBase->rPlace, 2);
		}

		return $this->render('pages/zeus/profile.html.twig', [
			'player_bases' => $playerBases,
			'has_splash_mode' => 'splash' === $request->query->get('mode'),
			'player' => $player,
			'player_experience' => $playerExperience,
			'player_missing_experience' => $playerMissingExperience,
			'player_experience_progress' => $playerExperienceProgress,
			'building_resource_refund' => $this->getParameter('athena.building.building_queue_resource_refund'),
		]);
	}

	/**
	 * @return array{
	 *	waiting_for_me: int,
	 *  waiting_for_other: int,
	 *  operational: int,
	 *  stand_by: int,
	 *  total: int,
	 *  max: int
	 * }
	 **/
	private function getCommercialRouteNumbers(
		SessionInterface $session,
		OrbitalBaseHelper $orbitalBaseHelper,
		CommercialRouteManager $commercialRouteManager,
		OrbitalBase $orbitalBase,
	): array {
		$routes = array_merge(
			$commercialRouteManager->getByBase($orbitalBase->getId()),
			$commercialRouteManager->getByDistantBase($orbitalBase->getId())
		);
		//if (0 === count($routes)) {
		//	return [];
		//}

		$nCRWaitingForOther = 0;
		$nCRWaitingForMe = 0;
		$nCROperational = 0;
		$nCRInStandBy = 0;

		foreach ($routes as $route) {
			if ($route->getStatement() == CommercialRoute::PROPOSED AND $route->getPlayerId1() == $session->get('playerId')) {
				$nCRWaitingForOther++;
			} elseif ($route->getStatement() == CommercialRoute::PROPOSED AND $route->getPlayerId1() != $session->get('playerId')) {
				$nCRWaitingForMe++;
			} elseif ($route->getStatement() == CommercialRoute::ACTIVE) {
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
			'max' => $orbitalBaseHelper->getBuildingInfo(
				OrbitalBaseResource::SPATIOPORT,
				'level',
				$orbitalBase->getLevelSpatioport(),
				'nbRoutesMax'
			),
		];
	}
}
