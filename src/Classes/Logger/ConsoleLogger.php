<?php

namespace App\Classes\Logger;

class ConsoleLogger extends AbstractLogger
{
	public function log($level, \Stringable|string $message, array $context = []): void
	{
		\file_put_contents('php://stdout', $this->formatMessage($level, $message, new \DateTime(), $context));
	}
}
