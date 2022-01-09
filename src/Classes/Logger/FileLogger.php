<?php

namespace App\Classes\Logger;

class FileLogger extends AbstractLogger
{
	public function __construct(
		protected string $logDirectory,
		protected ?int $logRotation = null
	) {
	}

	public function log($level, \Stringable|string $message, array $context = []): void
	{
		$dateTime = new \DateTime();
		\file_put_contents(
			$this->getFilePath($dateTime),
			$this->formatMessage($level, $message, $dateTime, $context),
			FILE_APPEND
		);
	}

	protected function getFilePath(\DateTime $dateTime): string
	{
		return \sprintf('%s/%s/%s.log', $this->logDirectory, $this->getType(), $dateTime->format('Y-m-d'));
	}
}
