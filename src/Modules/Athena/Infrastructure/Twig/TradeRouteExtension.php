<?php

namespace App\Modules\Athena\Infrastructure\Twig;

use App\Classes\Library\Game;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Gaia\Model\Place;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TradeRouteExtension extends AbstractExtension
{
	public function __construct(
		protected RequestStack $requestStack,
	) {

	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_route_price', function (float $distance) {
				$price = Game::getRCPrice($distance);

				if ($this->requestStack->getSession()->get('playerInfo')->get('color') == ColorResource::NEGORA) {
					// bonus if the player is from Negore
					$price -= round($price * ColorResource::BONUS_NEGORA_ROUTE / 100);
				}

				return $price;
			}),
			new TwigFunction('get_route_income', function (
				OrbitalBase $defaultBase,
				Place $place,
				float $distance,
				float $routeSectorBonus,
				float $routeColorBonus,
			) {
				$bonusA = ($defaultBase->sector != $place->rSector) ? $routeSectorBonus : 1;
				$bonusB = ($this->requestStack->getSession()->get('playerInfo')->get('color')) != $place->playerColor ? $routeColorBonus : 1;

				return Game::getRCIncome($distance, $bonusA, $bonusB);
			}),
		];
	}
}
