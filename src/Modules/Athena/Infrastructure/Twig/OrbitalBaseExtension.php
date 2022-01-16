<?php

namespace App\Modules\Athena\Infrastructure\Twig;

use App\Classes\Library\Game;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Resource\OrbitalBaseResource;
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
		];
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_base_production', fn (OrbitalBase $orbitalBase, int $level = null) => Game::resourceProduction(
				$this->orbitalBaseHelper->getBuildingInfo(
					OrbitalBaseResource::REFINERY,
					'level',
					$level ?? $orbitalBase->getLevelRefinery(),
					'refiningCoefficient'
				),
				$orbitalBase->getPlanetResources()
			)),
			new TwigFunction('get_building_info', fn (int $buildingNumber, string $info) => $this->orbitalBaseHelper->getInfo($buildingNumber, $info))
		];
	}
}
