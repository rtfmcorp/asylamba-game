<?php

namespace Asylamba\Classes\Logger;

abstract class AbstractLogger {
	
	const LOG_TYPE_PHP = 'php';
	const LOG_TYPE_CRON = 'cron';
	const LOG_TYPE_CTC = 'ctc';
	const LOG_TYPE_STATS = 'stats';
	const LOG_TYPE_CALL = 'call';
	
	const LOG_LEVEL_INFO = 'info';
	const LOG_LEVEL_NOTICE = 'notice';
	const LOG_LEVEL_WARNING = 'warning';
	const LOG_LEVEL_DEBUG = 'debug';
	const LOG_LEVEL_ERROR = 'error';
	const LOG_LEVEL_CRITICAL = 'critical';
	
	abstract public function log(string $message, string $level = self::LOG_LEVEL_DEBUG, string $type = self::LOG_TYPE_PHP): void;

	public function formatMessage(string $message, string $level, \DateTime $datetime): string
	{
		return "[{$datetime->format('H:i:s')}] ". strtoupper($level) . ": $message\n";
	}
}
