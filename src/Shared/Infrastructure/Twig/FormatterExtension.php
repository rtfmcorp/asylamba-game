<?php

namespace App\Shared\Infrastructure\Twig;

use App\Classes\Library\Chronos;
use App\Classes\Library\Format;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatterExtension extends AbstractExtension
{
	public function getFilters(): array
	{
		return [
			new TwigFilter('number', fn (int|float $number) => Format::numberFormat($number)),
			new TwigFilter('plural', fn (int|float $number) => Format::plural($number)),
			new TwigFilter('percent', fn (int $number, int $base) => Format::percent($number, $base)),
			new TwigFilter('lite_seconds', fn (int $seconds) => Chronos::secondToFormat($seconds, 'lite')),
			new TwigFilter('date', fn (string $date) => Chronos::transform($date)),
		];
	}
}
