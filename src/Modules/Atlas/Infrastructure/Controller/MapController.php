<?php

namespace App\Modules\Atlas\Infrastructure\Controller;

use App\Classes\Container\Params;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Manager\ConquestManager;
use App\Modules\Ares\Manager\LiveReportManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Artemis\Manager\SpyReportManager;
use App\Modules\Artemis\Model\SpyReport;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Manager\RecyclingMissionManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Gaia\Galaxy\GalaxyConfiguration;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Gaia\Manager\SectorManager;
use App\Modules\Gaia\Manager\SystemManager;
use App\Modules\Gaia\Model\Place;
use App\Modules\Gaia\Model\System;
use App\Modules\Promethee\Manager\TechnologyManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MapController extends AbstractController
{
	public function __invoke(
		Request $request,
		OrbitalBase $defaultBase,
		Player $currentPlayer,
		ConquestManager $conquestManager,
		CommanderManager $commanderManager,
		CommercialRouteManager $commercialRouteManager,
		PlaceManager $placeManager,
		SystemManager $systemManager,
		SectorManager $sectorManager,
		OrbitalBaseManager $orbitalBaseManager,
		TechnologyManager $technologyManager,
		RecyclingMissionManager $recyclingMissionManager,
		GalaxyConfiguration $galaxyConfiguration,
		SpyReportManager $spyReportManager,
		LiveReportManager $liveReportManager,
	): Response {
		$defaultPosition = $this->getDefaultPosition($request, $placeManager, $systemManager, $defaultBase);
		$selectedSystemData = [];
		$movingCommanders = $commanderManager->getPlayerCommanders($currentPlayer->getId(), [Commander::MOVING]);

		if (null !== $defaultPosition['system']) {
			$places = $placeManager->getSystemPlaces($defaultPosition['system']);
			$placesIds = array_map(fn (Place $place) => $place->id, $places);


			$spyReportManager->newSession();
			$spyReportManager->load(array('rPlayer' => $currentPlayer->getId(), 'rPlace' => $placesIds), ['dSpying', 'DESC'], [0, 30]);

			$basesCount = $orbitalBaseManager->getPlayerBasesCount($movingCommanders);

			$selectedSystemData = [
				'system' => $defaultPosition['system'],
				'places' => $places,
				'technologies' => $technologyManager->getPlayerTechnology($currentPlayer->getId()),
				'recycling_missions' => $recyclingMissionManager->getBaseActiveMissions($defaultBase->rPlace),
				'spy_reports' => $spyReportManager->getAll(),
				'combat_reports' => $liveReportManager->getAttackReportsByPlaces($currentPlayer->getId(), $placesIds),
				'colonization_cost' => $conquestManager->getColonizationCost($currentPlayer, $basesCount),
				'conquest_cost' => $conquestManager->getConquestCost($currentPlayer, $basesCount),
			];
		}

		return $this->render('pages/atlas/map.html.twig', array_merge([
			'sectors' => $sectorManager->getAll(),
			'systems' => $systemManager->getAll(),
			'player_bases' => $orbitalBaseManager->getPlayerBases($currentPlayer->getId()),
			'default_base' => $defaultBase,
			'default_position' => $defaultPosition,
			'default_map_parameters' => Params::$params,
			'galaxy_configuration' => $galaxyConfiguration,
			'commercial_routes' => $commercialRouteManager->getAllPlayerRoutes($currentPlayer),
			'local_commanders' => $commanderManager->getBaseCommanders(
				$defaultBase->getRPlace(),
				[Commander::AFFECTED, Commander::MOVING],
				['c.line' => 'DESC']
			),
			'moving_commanders' => $movingCommanders,
			'attacking_commanders' => array_merge(
				$commanderManager->getVisibleIncomingAttacks($currentPlayer->getId()),
				$commanderManager->getOutcomingAttacks($currentPlayer->getId())
			),
		], $selectedSystemData));
	}

	/**
	 * @return array{ x: int, y: int, system: System|null, place: Place|null, system_id: int }
	 */
	protected function getDefaultPosition(
		Request $request,
		PlaceManager $placeManager,
		SystemManager $systemManager,
		OrbitalBase $defaultBase,
	): array {
		# map default position
		$x = $defaultBase->getXSystem();
		$y = $defaultBase->getYSystem();
		$systemId = 0;
		$system = $place = null;

		// other default location
		// par place
		if ($request->query->has('place')) {
			if (($place = $placeManager->get($request->query->get('place'))) !== null) {
				$x = $place->getXSystem();
				$y = $place->getYSystem();
				$systemId = $place->getRSystem();
				$system = $systemManager->get($systemId);
			}
		// par système
		} elseif ($request->query->has('systemid')) {
			if (($system = $systemManager->get($request->query->get('systemid'))) !== null) {
				$x = $system->xPosition;
				$y = $system->yPosition;
				$systemId = $request->query->get('systemid');
			}
		// par coordonnée
		} elseif ($request->query->has('x') && $request->query->has('y')) {
			$x = $request->query->get('x');
			$y = $request->query->get('y');
		}
		return [
			'x' => $x,
			'y' => $y,
			'system_id' => $systemId,
			'system' => $system,
			'place' => $place,
		];
	}
}
