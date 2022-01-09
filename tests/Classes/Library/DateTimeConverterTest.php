<?php

namespace Tests\Asylamba\Classes\Library;

use App\Classes\Library\DateTimeConverter;
use PHPUnit\Framework\TestCase;

class DateTimeConverterTest extends TestCase
{
	/**
	 * @dataProvider provideMsData
	 */
	public function testConversionToMs(string $dateTime, int $expectedMs): void
	{
		$this->assertEquals($expectedMs, DateTimeConverter::to_ms($dateTime));
	}

	public function provideMsData(): \Generator
	{
		yield [
			'+1 minute',
			60 * 1000,
		];

		yield [
			'+1 hour',
			60 * 60 * 1000,
		];

		yield [
			'-6 days',
			0,
		];

		yield [
			'+30 days',
			30 * 24 * 60 * 60 * 1000,
		];
	}
}
