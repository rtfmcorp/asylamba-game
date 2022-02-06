<?php

namespace App\Shared\Infrastructure\Twig;

use App\Classes\Library\Utils;
use App\Shared\Domain\Model\TravellerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TravellerExtension extends AbstractExtension
{
	public function getFilters(): array
	{
		return [
			new TwigFilter('travel_spent_time', fn (TravellerInterface $traveller, bool $reversed = false) => $reversed
				? Utils::interval($traveller->getArrivalDate(), Utils::now(), 's')
				: Utils::interval($traveller->getDepartureDate(), Utils::now(), 's'),
			),
			new TwigFilter('travel_remaining_time', fn (TravellerInterface $traveller) => Utils::interval(
				Utils::now(),
				$traveller->getArrivalDate(),
				's',
			)),
			new TwigFilter('travel_total_time', fn (TravellerInterface $traveller) => Utils::interval(
				$traveller->getDepartureDate(),
				$traveller->getArrivalDate(),
				's',
			)),
		];
	}
}
