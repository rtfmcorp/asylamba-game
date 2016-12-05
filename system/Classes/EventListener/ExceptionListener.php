<?php

namespace Asylamba\Classes\EventListener;

use Asylamba\Classes\Worker\Logger;

use Asylamba\Classes\Event\ExceptionEvent;
use Asylamba\Classes\Event\ErrorEvent;

class ExceptionListener {
	/** @var Logger **/
	protected $logger;
	
	/**
	 * @param Logger $logger
	 */
	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}
	
	/**
	 * @param ExceptionEvent $event
	 */
	public function onCoreException(ExceptionEvent $event)
	{
		$exception = $event->getException();
		
		$this->logger->log("{$exception->getMessage()} at {$exception->getFile()} at line {$exception->getLine()}", Logger::LOG_LEVEL_ERROR);
	}
	
	public function onCoreError(ErrorEvent $event)
	{
		$error = $event->getError();
		
		$this->logger->log("{$error->getMessage()} at {$error->getFile()} at line {$error->getLine()}", Logger::LOG_LEVEL_CRITICAL);
	}
}