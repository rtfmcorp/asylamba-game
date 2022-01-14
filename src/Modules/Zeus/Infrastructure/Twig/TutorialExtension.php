<?php

namespace App\Modules\Zeus\Infrastructure\Twig;

use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Zeus\Resource\TutorialResource;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TutorialExtension extends AbstractExtension
{
	public function getFunctions(): array
	{
		return [
			new TwigFunction('tutorial_info', fn (int $step, string $info) => TutorialResource::getInfo($step, $info)),
			new TwigFunction('ship_name', fn (int $id) => ShipResource::getInfo($id, 'codeName'))
		];
	}
}
