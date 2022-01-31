<?php

namespace App\Modules\Ares\Infrastructure\Twig;

use App\Classes\Library\Game;
use App\Classes\Library\Utils;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Ares\Resource\CommanderResources;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CommanderExtension extends AbstractExtension
{
	public function __construct(protected CommanderManager $commanderManager)
	{
	}

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
			new TwigFunction('get_commander_level_up_from_report', fn (int $level, int $newExperience) => $this->commanderManager->nbLevelUp($level, $newExperience)),
			new TwigFunction('get_commander_missing_experience', fn (Commander $commander) => $this->commanderManager->experienceToLevelUp($commander)),
			new TwigFunction('get_fleet_cost', fn (Commander $commander) => Game::getFleetCost($commander->getNbrShipByType())),
			new TwigFunction('get_commander_position', fn (Commander $commander, int $x1, int $x2, int $y1, int $y2) => $this->commanderManager->getPosition($commander, $x1, $x2, $x2, $y2)),
			new TwigFunction('get_commander_rank', fn (int $level) => $this->getCommanderLevel($level)),
			new TwigFunction('get_commander_price', fn (Commander $commander, int $commanderCurrentRate) => ceil($commander->getExperience() * $commanderCurrentRate)),
		];
	}

	protected function getCommanderLevel(int $level): string
	{
		return CommanderResources::getInfo($level, 'grade');
	}
}
