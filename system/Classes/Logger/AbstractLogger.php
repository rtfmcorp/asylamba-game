<?php

namespace Asylamba\Classes\Logger;

use Psr\Log\LogLevel;

abstract class AbstractLogger implements \Psr\Log\LoggerInterface
{
	public function alert(\Stringable|string $message, array $context = []): void
	{
		$this->log($message, LogLevel::ALERT, $context);
	}

	public function critical(\Stringable|string $message, array $context = []): void
	{
		$this->log($message, LogLevel::CRITICAL, $context);
	}

	public function debug(\Stringable|string $message, array $context = []): void
	{
		$this->log($message, LogLevel::DEBUG, $context);
	}

	public function emergency(\Stringable|string $message, array $context = []): void
	{
		$this->log($message, LogLevel::EMERGENCY, $context);
	}

	public function error(\Stringable|string $message, array $context = []): void
	{
		$this->log($message, LogLevel::ERROR, $context);
	}

	public function info(\Stringable|string $message, array $context = []): void
	{
		$this->log($message, LogLevel::INFO, $context);
	}

	public function notice(\Stringable|string $message, array $context = []): void
	{
		$this->log($message, LogLevel::NOTICE, $context);
	}

	public function warning(\Stringable|string $message, array $context = []): void
	{
		$this->log($message, LogLevel::WARNING, $context);
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
