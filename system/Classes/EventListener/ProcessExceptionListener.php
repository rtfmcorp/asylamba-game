<?php

namespace Asylamba\Classes\EventListener;

use Asylamba\Classes\Logger\AbstractLogger;

use Asylamba\Classes\Event\ProcessExceptionEvent;
use Asylamba\Classes\Event\ProcessErrorEvent;

use Asylamba\Classes\Process\ProcessGateway;

use Asylamba\Classes\Database\Database;

class ProcessExceptionListener
{
	public function __construct(
		protected AbstractLogger $logger,
		protected Database $database,
		protected ProcessGateway $processGateway,
		protected string $processName
	) {
	}

	public function onCoreException(ProcessExceptionEvent $event): void
	{
		$exception = $event->getException();
		$this->process(
			$event,
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			$exception->getTraceAsString(),
			AbstractLogger::LOG_LEVEL_ERROR
		);
	}

	public function onCoreError(ProcessErrorEvent $event): void
	{
		$error = $event->getError();
		$this->process(
			$event,
			$error->getMessage(),
			$error->getFile(),
			$error->getLine(),
			$error->getTraceAsString(),
			AbstractLogger::LOG_LEVEL_CRITICAL
		);
	}

	public function process(object $event, string $message, string $file, int $line, string $trace, string $level): void
	{
		$this->logger->log(json_encode([
			'process' => $this->processName,
			'message' => $message,
			'file' => $file,
			'line' => $line,
			'task' => $event->getTask()
		]), $level);
		
		if ($this->database->inTransaction()) {
			$this->database->rollBack();
		}
		
		$this->processGateway->writeToMaster([
			'success' => false,
			'task' => $event->getTask(),
			'error' => [
				'message' => $message,
				'file' => $file,
				'line' => $line,
				'level' => $level
			]
		]);
	}
}
