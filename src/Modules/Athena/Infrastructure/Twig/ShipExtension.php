<?php

namespace App\Modules\Athena\Infrastructure\Twig;

use App\Modules\Athena\Resource\ShipResource;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ShipExtension extends AbstractExtension
{
	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_ship_info', fn (int $shipNumber, string $info) => ShipResource::getInfo($shipNumber, $info)),
			new TwigFunction('get_ship_price', fn (int $shipNumber, int $shipCurrentRate) => $shipCurrentRate * ShipResource::getInfo($shipNumber, 'resourcePrice')),
		];
	}
}
