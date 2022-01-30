<?php

namespace App\Modules\Athena\Infrastructure\Twig;

use App\Classes\Library\Game;
use App\Classes\Library\Utils;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Model\Transaction;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Gaia\Resource\PlaceResource;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class OrbitalBaseExtension extends AbstractExtension
{
	public function __construct(
		protected OrbitalBaseHelper $orbitalBaseHelper,
	) {

	}

	public function getFilters(): array
	{
		return [
			new TwigFilter('base_demography', fn (OrbitalBase $orbitalBase) => Game::getSizeOfPlanet($orbitalBase->getPlanetPopulation())),
			new TwigFilter('base_type', fn (OrbitalBase $orbitalBase) => PlaceResource::get($orbitalBase->typeOfBase, 'name')),
			new TwigFilter('scalar_base_type', fn (string $type) => PlaceResource::get($type, 'name')),
			new TwigFilter('base_storage_percent', fn (OrbitalBase $orbitalBase) => $this->orbitalBaseHelper->getStoragePercent($orbitalBase)),
			new TwigFilter('base_coords', fn(OrbitalBase $orbitalBase) => Game::formatCoord($orbitalBase->getXSystem(), $orbitalBase->getYSystem(), $orbitalBase->getPosition(), $orbitalBase->getSector()))
		];
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_base_type_info', fn (string $baseType, string $info) => PlaceResource::get($baseType, $info)),
			new TwigFunction('can_leave_base', fn (OrbitalBase $orbitalBase) => Utils::interval(Utils::now(), $orbitalBase->dCreation, 'h') < OrbitalBase::COOL_DOWN),
			new TwigFunction('get_time_until_cooldown_end', fn (OrbitalBase $orbitalBase) => OrbitalBase::COOL_DOWN * 60 * 60 - Utils::interval(Utils::now(), $orbitalBase->dCreation, 's')),
			new TwigFunction('get_base_production', fn (OrbitalBase $orbitalBase, int $level = null) => Game::resourceProduction(
				$this->orbitalBaseHelper->getBuildingInfo(
					OrbitalBaseResource::REFINERY,
					'level',
					$level ?? $orbitalBase->getLevelRefinery(),
					'refiningCoefficient'
				),
				$orbitalBase->getPlanetResources()
			)),
			new TwigFunction('get_building_info', fn (int $buildingNumber, string $info, int $level = 0, string $sub = 'default') => $this->orbitalBaseHelper->getInfo($buildingNumber, $info, $level, $sub)),
			new TwigFunction('get_building_level_range', fn (int $currentLevel) => \range(
				($currentLevel < 3) ? 1 : $currentLevel - 2,
				(($currentLevel > 35) ? 41 : $currentLevel + 5) - 1,
			)),
			new TwigFunction('get_base_fleet_cost', fn (OrbitalBase $base) => Game::getFleetCost($base->shipStorage, false)),
			new TwigFunction('get_base_tax', fn (OrbitalBase $base, int $taxCoeff) => Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase, $taxCoeff)),
			// @TODO move to a rightful place
			new TwigFunction('get_ship_transaction_cost', fn (Transaction $transaction) => ShipResource::getInfo($transaction->identifier, 'cost') * ShipResource::COST_REDUCTION * $transaction->quantity),
		];
	}
}
