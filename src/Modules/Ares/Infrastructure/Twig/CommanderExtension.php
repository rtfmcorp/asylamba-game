<?php

namespace App\Modules\Ares\Infrastructure\Twig;

use App\Classes\Library\Utils;
use App\Modules\Ares\Model\Commander;
use App\Modules\Ares\Resource\CommanderResources;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CommanderExtension extends AbstractExtension
{
	public function getFilters(): array
	{
		return [
			new TwigFilter('mission_label', fn (Commander $commander) => match ($commander->travelType) {
				Commander::MOVE => 'déplacement vers ' . $commander->destinationPlaceName,
				Commander::LOOT => 'pillage de ' . $commander->destinationPlaceName,
				Commander::COLO => 'colonisation de ' . $commander->destinationPlaceName,
				Commander::BACK => 'retour vers ' . $commander->destinationPlaceName,
				default => 'autre'
			}),
			new TwigFilter('commander_rank', fn (Commander $commander) => $this->getCommanderLevel($commander->level)),
		];
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_commander_rank', fn (int $level) => $this->getCommanderLevel($level)),
			new TwigFunction('get_commander_price', fn (Commander $commander, int $commanderCurrentRate) => ceil($commander->getExperience() * $commanderCurrentRate)),
		];
	}

	protected function getCommanderLevel(int $level): string
	{
		return CommanderResources::getInfo($level, 'grade');
	}
}
