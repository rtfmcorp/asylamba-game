<?php

namespace App\Modules\Athena\Infrastructure\Twig;

use App\Classes\Library\Game;
use App\Classes\Library\Utils;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Model\BuildingQueue;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Model\ShipQueue;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Gaia\Resource\PlaceResource;
use App\Modules\Promethee\Helper\TechnologyHelper;
use App\Modules\Promethee\Model\TechnologyQueue;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class OrbitalBaseExtension extends AbstractExtension
{
	public function __construct(
		protected OrbitalBaseHelper $orbitalBaseHelper,
		protected TechnologyHelper $technologyHelper,
	) {

	}

	public function getFilters(): array
	{
		return [
			new TwigFilter('base_demography', fn (OrbitalBase $orbitalBase) => Game::getSizeOfPlanet($orbitalBase->getPlanetPopulation())),
			new TwigFilter('base_type', fn (OrbitalBase $orbitalBase) => PlaceResource::get($orbitalBase->typeOfBase, 'name')),
			new TwigFilter('scalar_base_type', fn (string $type) => PlaceResource::get($type, 'name')),
			new TwigFilter('base_max_technology_queues', fn (OrbitalBase $orbitalBase) => $this->orbitalBaseHelper->getBuildingInfo(
				OrbitalBaseResource::TECHNOSPHERE,
				'level',
				$orbitalBase->levelTechnosphere,
				'nbQueues'
			)),
			new TwigFilter('technology_queue_time', fn (TechnologyQueue $technologyQueue) => $this->technologyHelper->getInfo(
				$technologyQueue->technology,
				'time',
				$technologyQueue->targetLevel
			)),
			new TwigFilter('technology_queue_duration', fn (TechnologyQueue $technologyQueue) => Utils::interval(Utils::now(), $technologyQueue->dEnd, 's')),
			new TwigFilter('technology_queue_picture', fn (TechnologyQueue $technologyQueue) => $this->technologyHelper->getInfo($technologyQueue->technology, 'imageLink')),
			new TwigFilter('technology_queue_name', fn (TechnologyQueue $technologyQueue) => $this->technologyHelper->getInfo($technologyQueue->technology, 'name')),
			new TwigFilter('is_unblocking_technology', fn (TechnologyQueue $technologyQueue) => (!$this->technologyHelper->isAnUnblockingTechnology($technologyQueue->technology))),
			new TwigFilter('base_max_dock1_ship_queues', fn (OrbitalBase $orbitalBase) => $this->orbitalBaseHelper->getBuildingInfo(
				OrbitalBaseResource::DOCK1,
				'level',
				$orbitalBase->levelDock1,
				'nbQueues'
			)),
			new TwigFilter('ship_queue_time', fn (ShipQueue $shipQueue) => $shipQueue->quantity * ShipResource::getInfo($shipQueue->shipNumber, 'time')),
			new TwigFilter('ship_queue_duration', fn (ShipQueue $shipQueue) => Utils::interval(Utils::now(), $shipQueue->dEnd, 's')),
			new TwigFilter('ship_queue_picture', fn (ShipQueue $shipQueue) => ShipResource::getInfo($shipQueue->shipNumber, 'imageLink')),
			new TwigFilter('ship_queue_name', fn (ShipQueue $shipQueue) => ShipResource::getInfo($shipQueue->shipNumber, 'codeName')),
			new TwigFilter('base_max_building_queues', fn (OrbitalBase $orbitalBase) => $this->orbitalBaseHelper->getBuildingInfo(
				OrbitalBaseResource::GENERATOR,
				'level',
				$orbitalBase->levelGenerator,
				'nbQueues',
			)),
			new TwigFilter('building_queue_duration', fn (BuildingQueue $buildingQueue) => Utils::interval(Utils::now(), $buildingQueue->dEnd, 's')),
			new TwigFilter('building_queue_time', fn (BuildingQueue $buildingQueue) => $this->orbitalBaseHelper->getBuildingInfo(
				$buildingQueue->buildingNumber,
				'level',
				$buildingQueue->targetLevel,
				'time',
			)),
			new TwigFilter('building_queue_picture', fn (BuildingQueue $buildingQueue) => $this->orbitalBaseHelper->getBuildingInfo(
				$buildingQueue->buildingNumber,
				'imageLink',
			)),
			new TwigFilter('building_queue_name', fn (BuildingQueue $buildingQueue) => $this->orbitalBaseHelper->getBuildingInfo(
				$buildingQueue->buildingNumber,
				'frenchName',
			)),
		];
	}
}
