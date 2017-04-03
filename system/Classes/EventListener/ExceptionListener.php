<?php

namespace Asylamba\Classes\EventListener;

use Asylamba\Classes\Worker\Logger;
use Asylamba\Classes\Container\Session;
use Asylamba\Classes\Library\Flashbag;

use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;

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
			$event,
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			Logger::LOG_LEVEL_ERROR,
			($exception instanceof FormException) ? Flashbag::TYPE_FORM_ERROR : Flashbag::TYPE_STD_ERROR
		);
	}
	
	/**
	 * @param ErrorEvent $event
	 */
	public function onCoreError(ErrorEvent $event)
	{
		$error = $event->getError();
		$this->process(
			$event,
			$error->getMessage(),
			$error->getFile(),
			$error->getLine(),
			Logger::LOG_LEVEL_CRITICAL,
			Flashbag::TYPE_BUG_ERROR
		);
	}
	
	/**
	 * @param $event
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param string $level
	 * @param int $flashbagLevel
	 */
	public function process($event, $message, $file, $line, $level, $flashbagLevel)
	{
		$this->logger->log("$message at $file at line $line", $level);
		
		$this->session->addFlashbag($message, $flashbagLevel);
		
		$response = new Response();
		$response->redirect($this->getRedirection($event->getRequest()));
		$event->setResponse($response);
	}
	
	/**
	 * @param Request $request
	 * @return string
	 */
	public function getRedirection(Request $request)
	{
		$history = $this->session->getHistory();

		if (($nbPaths = count($history)) === 0) {
			return '/';
		}
		if (($redirect = '/' . $this->session->getLastHistory()) === $request->getPath()) {
			// We get the path before the last one if available
			return ($nbPaths > 1) ? $history[$nbPaths - 2] : '/';
		}
		return $redirect;
	}
}