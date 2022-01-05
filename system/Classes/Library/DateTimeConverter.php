<?php

namespace Asylamba\Classes\Library;

use Symfony\Component\Messenger\Stamp\DelayStamp;

class DateTimeConverter
{
	public static function to_delay_stamp(string $dateTime): DelayStamp
	{
		return new DelayStamp(self::to_ms($dateTime));
	}

	public static function to_ms(string $dateTime): int
	{
		$diff = strtotime($dateTime) - strtotime('now');

		return ($diff > 0) ? $diff * 1000 : 0;
	}
}
