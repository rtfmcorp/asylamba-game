<?php

namespace Asylamba\Classes\EventListener;

use Asylamba\Classes\Event\ProcessExceptionEvent;
use Asylamba\Classes\Event\ProcessErrorEvent;

use Asylamba\Classes\Process\ProcessGateway;

use Asylamba\Classes\Database\Database;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ProcessExceptionListener
{
	public function __construct(
		protected LoggerInterface $logger,
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
			LogLevel::ERROR,
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
			LogLevel::CRITICAL,
		);
	}

	public function process(object $event, string $message, string $file, int $line, string $trace, string $level): void
	{
		$this->logger->log($level, $message, [
			'process' => $this->processName,
			'file' => $file,
			'line' => $line,
			'task' => $event->getTask()
		]);
		
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
