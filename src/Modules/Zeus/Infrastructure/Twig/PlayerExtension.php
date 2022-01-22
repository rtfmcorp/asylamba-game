<?php

namespace App\Modules\Zeus\Infrastructure\Twig;

use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Demeter\Resource\ColorResource;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PlayerExtension extends AbstractExtension
{
	public function __construct(protected OrbitalBaseManager $orbitalBaseManager)
	{

	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_faction_info', fn (int $factionId, string $info) => ColorResource::getInfo($factionId, $info)),
			new TwigFunction('get_player_bases_count', fn (array $movingCommanders) => $this->orbitalBaseManager->getPlayerBasesCount($movingCommanders))
		];
	}
}
