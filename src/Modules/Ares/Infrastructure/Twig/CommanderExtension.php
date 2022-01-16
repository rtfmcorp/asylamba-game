<?php

namespace App\Modules\Ares\Infrastructure\Twig;

use App\Classes\Library\Utils;
use App\Modules\Ares\Model\Commander;
use App\Modules\Ares\Resource\CommanderResources;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CommanderExtension extends AbstractExtension
{
	public function getFilters(): array
	{
		return [
			new TwigFilter('journey_remaining_time', fn (Commander $commander) => Utils::interval(Utils::now(), $commander->dArrival, 's')),
			new TwigFilter('journey_total_time', fn (Commander $commander) => Utils::interval($commander->dStart, $commander->dArrival, 's')),
			new TwigFilter('mission_label', fn (Commander $commander) => match ($commander->travelType) {
				Commander::MOVE => 'déplacement vers ' . $commander->destinationPlaceName,
				Commander::LOOT => 'pillage de ' . $commander->destinationPlaceName,
				Commander::COLO => 'colonisation de ' . $commander->destinationPlaceName,
				Commander::BACK => 'retour vers ' . $commander->destinationPlaceName,
				default => 'autre'
			}),
			new TwigFilter('commander_rank', fn (Commander $commander) => CommanderResources::getInfo($commander->level, 'grade')),
		];
	}
}
