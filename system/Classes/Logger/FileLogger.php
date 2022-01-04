<?php

namespace Asylamba\Classes\Logger;

class FileLogger extends AbstractLogger
{
	public function __construct(
		protected string $logDirectory,
		protected ?int $logRotation = null
	) {
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function log(string $message, string $level = self::LOG_LEVEL_DEBUG, string $type = self::LOG_TYPE_PHP): void
	{
		$datetime = new \DateTime();
		\file_put_contents(
			"{$this->logDirectory}/$type/{$datetime->format('Y-m-d')}.log",
			$this->formatMessage($message, $level, $datetime),
			FILE_APPEND
		);
	}
}
