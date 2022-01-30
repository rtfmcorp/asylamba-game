<?php

namespace App\Modules\Atlas\Infrastructure\Twig;

use App\Classes\Library\Game;
use App\Modules\Ares\Domain\Specification\Player\CanOrbitalBaseTradeWithPlace;
use App\Modules\Ares\Domain\Specification\Player\CanPlayerAttackPlace;
use App\Modules\Ares\Domain\Specification\Player\CanPlayerMoveToPlace;
use App\Modules\Ares\Domain\Specification\Player\CanRecycle;
use App\Modules\Ares\Domain\Specification\Player\CanSpyPlace;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Model\CommercialRoute;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Gaia\Model\Place;
use App\Modules\Gaia\Model\System;
use App\Modules\Gaia\Resource\SystemResource;
use App\Modules\Zeus\Model\Player;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MapExtension extends AbstractExtension
{
	public function __construct(
		protected RequestStack $requestStack,
		protected CommercialRouteManager $commercialRouteManager,
		protected PlaceManager $placeManager,
	) {

	}

	public function getFilters(): array
	{
		return [
			new TwigFilter('coords', fn (System $system) => Game::formatCoord($system->xPosition, $system->yPosition)),
		];
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_place', fn(int $placeId) => $this->placeManager->get($placeId)),
			new TwigFunction('get_base_antispy_radius', fn (OrbitalBase $base) => Game::getAntiSpyRadius($base->getAntiSpyAverage())),
			new TwigFunction('get_travel_time', fn (OrbitalBase $defaultBase, Place $place) => Game::getTimeTravel(
				$defaultBase->system,
				$defaultBase->position,
				$defaultBase->xSystem,
				$defaultBase->ySystem,
				$place->rSystem,
				$place->position,
				$place->xSystem,
				$place->ySystem,
				$this->requestStack->getSession()->get('playerBonus'),
			)),
			new TwigFunction('get_place_type', fn (Place $place) => Game::convertPlaceType($place->typeOfPlace)),
			new TwigFunction('get_system_info', fn (int $systemType, string $info) => SystemResource::getInfo($systemType, $info)),
			new TwigFunction('get_place_distance', fn (OrbitalBase $defaultBase, Place $place) => Game::getDistance($defaultBase->xSystem, $place->xSystem, $defaultBase->ySystem, $place->ySystem)),
			new TwigFunction('get_max_travel_distance', fn () => Game::getMaxTravelDistance($this->requestStack->getSession()->get('playerBonus'))),
			new TwigFunction('get_place_demography', fn (Place $place) => Game::getSizeOfPlanet($place->population)),
			new TwigFunction('get_place_technosphere_improvement_coeff', fn (Place $place) => Game::getImprovementFromScientificCoef($place->coefHistory)),
			new TwigFunction('get_commercial_route_data', fn (OrbitalBase $defaultBase, Place $place) => $this->getCommercialRouteData($defaultBase, $place)),

			new TwigFunction('can_player_attack_place', function (Player $player, Place $place) {
				$specification = new CanPlayerAttackPlace($player);

				return $specification->isSatisfiedBy($place);
			}),
			new TwigFunction('can_player_move_to_place', function (Player $player, Place $place, OrbitalBase $orbitalBase) {
				$specification = new CanPlayerMoveToPlace($player, $orbitalBase);

				return $specification->isSatisfiedBy($place);
			}),
			new TwigFunction('can_orbital_base_trade_with_place', function (OrbitalBase $orbitalBase, Place $place) {
				$specification = new CanOrbitalBaseTradeWithPlace($orbitalBase);

				return $specification->isSatisfiedBy($place);
			}),
			new TwigFunction('can_spy', function (Player $player, Place $place) {
				$specification = new CanSpyPlace($player);

				return $specification->isSatisfiedBy($place);
			}),
			new TwigFunction('can_recycle', function (Player $player, Place $place) {
				$specification = new CanRecycle($player);

				return $specification->isSatisfiedBy($place);
			}),
		];
	}

	private function getCommercialRouteData(OrbitalBase $defaultBase, Place $place): array
	{
		$routes = array_merge(
			$this->commercialRouteManager->getByBase($defaultBase->getId()),
			$this->commercialRouteManager->getByDistantBase($defaultBase->getId())
		);

		$data = [
			'proposed' => false,
			'not_accepted' => false,
			'stand_by' => false,
			'send_resources' => false,
			'slots' => \count($routes),
		];

		foreach ($routes as $route) {
			if ($route->getROrbitalBaseLinked() == $defaultBase->rPlace && $route->statement == CommercialRoute::PROPOSED) {
				$data['slots']--;
			}
			if ($place->getId() !== $route->getROrbitalBase() && $place->getId() !== $route->getROrbitalBaseLinked()) {
				continue;
			}
			$data = array_merge($data, match ($route->getStatement()) {
				CommercialRoute::PROPOSED => ($defaultBase->getId() === $route->getROrbitalBase())
						? ['proposed' => true]
						: ['not_accepted' => true],
				CommercialRoute::ACTIVE => ['send_resources' => true],
				CommercialRoute::STANDBY => ['stand_by' => true],
			});
		}
		return $data;
	}
}
