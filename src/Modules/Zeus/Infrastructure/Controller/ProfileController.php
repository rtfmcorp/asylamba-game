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
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProfileController extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		OrbitalBaseManager $orbitalBaseManager,
		CommercialRouteManager $commercialRouteManager,
		BuildingQueueManager $buildingQueueManager,
		ShipQueueManager $shipQueueManager,
		PlayerManager $playerManager,
		EntityManager $entityManager,
		TechnologyHelper $technologyHelper,
	): Response {
		// @TODO All this stuff needs to go in a dedicated service which will hold the logic
		$baseLevelPlayer = $this->getParameter('zeus.player.base_level');
		$playerMissingExperience = $baseLevelPlayer * (pow(2, ($currentPlayer->getLevel() - 1)));
		// @TODO Not quite sure that this is the next experience level. To check and rename accordingly
		$playerNextLevelExperience = $baseLevelPlayer * (pow(2, ($currentPlayer->getLevel() - 2)));
		$playerExperienceProgress = ((($currentPlayer->getExperience() - $playerNextLevelExperience) * 200) / $playerMissingExperience);

		// $sessionToken = $session->get('token');

		$playerBases = $orbitalBaseManager->getPlayerBases($currentPlayer->getId());

		foreach ($playerBases as $orbitalBase) {
			// @TODO: move it to the using part of the code and remove useless data
			if ($orbitalBase->getLevelSpatioport() > 0) {
				$orbitalBase->commercialRoutesData = $commercialRouteManager->getBaseCommercialData($orbitalBase);
			}

			// @TODO Move to dedicated service
			$orbitalBase->dock1ShipQueues = $shipQueueManager->getByBaseAndDockType($orbitalBase->rPlace, 1);
			$orbitalBase->dock2ShipQueues = $shipQueueManager->getByBaseAndDockType($orbitalBase->rPlace, 2);
		}

		return $this->render('pages/zeus/profile.html.twig', [
			'player_bases' => $playerBases,
			'has_splash_mode' => 'splash' === $request->query->get('mode'),
			'player_experience' => $currentPlayer->getExperience(),
			'player_missing_experience' => $playerMissingExperience,
			'player_experience_progress' => $playerExperienceProgress,
			'building_resource_refund' => $this->getParameter('athena.building.building_queue_resource_refund'),
		]);
	}
}
