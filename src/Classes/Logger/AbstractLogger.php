<?php

namespace App\Classes\Logger;

use Psr\Log\LogLevel;

abstract class AbstractLogger implements \Psr\Log\LoggerInterface
{
	public function alert(\Stringable|string $message, array $context = []): void
	{
		$this->log(LogLevel::ALERT, $message, $context);
	}

	public function critical(\Stringable|string $message, array $context = []): void
	{
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	public function debug(\Stringable|string $message, array $context = []): void
	{
		$this->log(LogLevel::DEBUG, $message, $context);
	}

	public function emergency(\Stringable|string $message, array $context = []): void
	{
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}

	public function error(\Stringable|string $message, array $context = []): void
	{
		$this->log(LogLevel::ERROR, $message, $context);
	}

	public function info(\Stringable|string $message, array $context = []): void
	{
		$this->log(LogLevel::INFO, $message, $context);
	}

	public function notice(\Stringable|string $message, array $context = []): void
	{
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	public function warning(\Stringable|string $message, array $context = []): void
	{
		$this->log(LogLevel::WARNING, $message, $context);
	}

	public function formatMessage(string $level, string $message, \DateTime $datetime, array $context = []): string
	{
		return \sprintf(
			"[%s] %s: %s {%s}\n",
			$datetime->format('H:i:s'),
			strtoupper($level),
			$message,
			json_encode($context),
		);
	}

	protected function getType(): string
	{
		return match (PROCESS_NAME) {
			'application' => 'php',
			default => 'proc',
		};
	}
}
