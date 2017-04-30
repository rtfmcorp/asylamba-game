<?php

namespace Asylamba\Classes\EventListener;

use Asylamba\Classes\Logger\AbstractLogger;

use Asylamba\Classes\Event\ProcessExceptionEvent;
use Asylamba\Classes\Event\ProcessErrorEvent;

use Asylamba\Classes\Process\ProcessGateway;

class ProcessExceptionListener {
	/** @var AbstractLogger **/
	protected $logger;
	/** @var string **/
	protected $processName;
	
	/**
	 * @param AbstractLogger $logger
	 * @param string $processName
	 */
	public function __construct(AbstractLogger $logger, $processName)
	{
		$this->logger = $logger;
		$this->processName = $processName;
	}
	
	/**
	 * @param ProcessExceptionEvent $event
	 */
	public function onCoreException(ProcessExceptionEvent $event)
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
	
	/**
	 * @param ProcessErrorEvent $event
	 */
	public function onCoreError(ProcessErrorEvent $event)
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
	
	/**
	 * @param $event
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param string $trace
	 * @param string $level
	 */
	public function process($event, $message, $file, $line, $trace, $level)
	{
		$this->logger->log("{$this->processName} : $message at $file at line $line\n$trace", $level);
		
		$this->processGateway->writeToMaster([
			'success' => false,
			'error' => [
				'message' => $message,
				'file' => $file,
				'line' => $line,
				'level' => $level
			]
		]);
	}
}