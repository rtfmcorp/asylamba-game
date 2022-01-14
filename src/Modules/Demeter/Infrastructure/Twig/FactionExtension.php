<?php

namespace App\Modules\Demeter\Infrastructure\Twig;

use App\Modules\Demeter\Resource\ColorResource;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FactionExtension extends AbstractExtension
{
	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_faction_statuses', fn (int $factionId) => $this->getFactionStatuses($factionId)),
			new TwigFunction('get_faction_name', fn (int $factionId) => $this->getFactionName($factionId)),
		];
	}

	protected function getFactionStatuses(int $factionId): array
	{
		return ColorResource::getInfo($factionId, 'status');
	}

	protected function getFactionName(int $factionId): string
	{
		return ColorResource::getInfo($factionId, 'popularName');
	}
}
