<?php

namespace Asylamba\Classes\EventListener;

use Asylamba\Classes\Worker\Logger;
use Asylamba\Classes\Container\Session;
use Asylamba\Classes\Library\Flashbag;

use Asylamba\Classes\Event\ExceptionEvent;
use Asylamba\Classes\Event\ErrorEvent;
use Asylamba\Classes\Exception\FormException;

class ExceptionListener {
	/** @var Logger **/
	protected $logger;
	/** @var Session **/
	protected $session;
	
	/**
	 * @param Logger $logger
	 * @param Session $session
	 */
	public function __construct(Logger $logger, Session $session)
	{
		$this->logger = $logger;
		$this->session = $session;
	}
	
	/**
	 * @param ExceptionEvent $event
	 */
	public function onCoreException(ExceptionEvent $event)
	{
		$exception = $event->getException();
		$this->process(
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			Logger::LOG_LEVEL_ERROR,
			($exception instanceof FormException) ? Flashbag::TYPE_FORM_ERROR : Flashbag::TYPE_STD_ERROR
		);
	}
	
	public function onCoreError(ErrorEvent $event)
	{
		$error = $event->getError();
		$this->process(
			$error->getMessage(),
			$error->getFile(),
			$error->getLine(),
			Logger::LOG_LEVEL_CRITICAL,
			Flashbag::TYPE_BUG_ERROR
		);
	}
	
	/**
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param string $level
	 * @param int $flashbagLevel
	 */
	public function process($message, $file, $line, $level, $flashbagLevel)
	{
		$this->logger->log("$message at $file at line $line", $level);
		
		$this->session->addFlashbag($message, $flashbagLevel);
		
		if (ob_get_level() > 0) {
			ob_end_clean();
		}
	}
}