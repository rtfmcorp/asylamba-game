<?php

namespace App\Shared\Infrastructure\Twig;

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
use App\Shared\Domain\Model\QueueableInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

// @TODO implement the rest of the generic methods and migrate ship logic
class QueueableExtension extends AbstractExtension
{
	public function __construct(
		protected OrbitalBaseHelper $orbitalBaseHelper,
		protected TechnologyHelper $technologyHelper,
	) {

	}

	public function getFilters(): array
	{
		return [
			// @TODO migrate to a technology twig extension
			new TwigFilter('is_unblocking_technology', fn (TechnologyQueue $technologyQueue) => (!$this->technologyHelper->isAnUnblockingTechnology($technologyQueue->technology))),
			new TwigFilter('queue_duration', fn (QueueableInterface $queue) => Utils::interval(Utils::now(), $queue->getEndDate(), 's')),
			new TwigFilter('queue_picture', fn (QueueableInterface $queue) => $this->getQueueHelper($queue)->getInfo($queue->getResourceIdentifier(), 'imageLink')),
			new TwigFilter('queue_name', fn (QueueableInterface $queue) => $this->getQueueHelper($queue)->getInfo($queue->getResourceIdentifier(), $this->getQueueNameKey($queue))),
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
			new TwigFilter('base_max_dock1_ship_queues', fn (OrbitalBase $orbitalBase) => $this->orbitalBaseHelper->getBuildingInfo(
				OrbitalBaseResource::DOCK1,
				'level',
				$orbitalBase->levelDock1,
				'nbQueues'
			)),
			new TwigFilter('ship_queue_time', fn (ShipQueue $shipQueue) => $shipQueue->quantity * ShipResource::getInfo($shipQueue->shipNumber, 'time')),
			new TwigFilter('ship_queue_picture', fn (ShipQueue $shipQueue) => ShipResource::getInfo($shipQueue->shipNumber, 'imageLink')),
			new TwigFilter('ship_queue_name', fn (ShipQueue $shipQueue) => ShipResource::getInfo($shipQueue->shipNumber, 'codeName')),
			new TwigFilter('base_max_building_queues', fn (OrbitalBase $orbitalBase) => $this->orbitalBaseHelper->getBuildingInfo(
				OrbitalBaseResource::GENERATOR,
				'level',
				$orbitalBase->levelGenerator,
				'nbQueues',
			)),
			// @TODO check why building helper needs the sup level
			new TwigFilter('building_queue_time', fn (BuildingQueue $buildingQueue) => $this->orbitalBaseHelper->getBuildingInfo(
				$buildingQueue->buildingNumber,
				'level',
				$buildingQueue->targetLevel,
				'time',
			)),
		];
	}

	protected function getQueueHelper(QueueableInterface $queueable): OrbitalBaseHelper|TechnologyHelper
	{
		return match(get_class($queueable)) {
			BuildingQueue::class => $this->orbitalBaseHelper,
			TechnologyQueue::class => $this->technologyHelper,
		};
	}

	protected function getQueueNameKey(QueueableInterface $queueable): string
	{
		return match(get_class($queueable)) {
			BuildingQueue::class => 'frenchName',
			TechnologyQueue::class => 'name',
		};
	}
}
