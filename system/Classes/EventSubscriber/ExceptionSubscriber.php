<?php

namespace Asylamba\Classes\EventSubscriber;

use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Event\ExceptionEvent;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Library\Session\SessionWrapper;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
	public function __construct(
		protected LoggerInterface $logger,
		protected SessionWrapper $sessionWrapper,
		protected Database $database,
		protected string $templatePath,
	) {
	}

	public static function getSubscribedEvents()
	{
		return [
			ExceptionEvent::class => [
				['onCoreException'],
			],
			WorkerMessageFailedEvent::class => [
				['onWorkerException'],
			],
		];
	}

	public function onCoreException(ExceptionEvent $event): void
	{
		$exception = $event->getThrowable();

		\Sentry\captureException($exception);

		$this->process(
			$event,
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			($exception instanceof FormException) ? '' : $exception->getTraceAsString(),
			LogLevel::ERROR,
			($exception instanceof FormException) ? Flashbag::TYPE_FORM_ERROR : Flashbag::TYPE_STD_ERROR,
			($exception instanceof FormException) ? $exception->getRedirect() : null
		);
	}

	public function onWorkerException(WorkerMessageFailedEvent $event): void
	{
		$throwable = $event->getThrowable();

		\Sentry\captureException($throwable);

		$this->logger->log(
			LogLevel::CRITICAL,
			'Handler failed to execute: {message} at {file}.l{line}. Trace: {trace}',
			[
				'message' => $throwable->getMessage(),
				'file' => $throwable->getFile(),
				'line' => $throwable->getLine(),
				'trace' => $throwable->getTraceAsString(),
			],
		);

		if ($this->database->inTransaction()) {
			$this->database->rollBack();
		}
	}
	
	/**
	 * @param $event
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param string $trace
	 * @param string $level
	 * @param int $flashbagLevel
	 */
	public function process($event, $message, $file, $line, $trace, $level, $flashbagLevel, $redirect = null)
	{
		$request = $event->getRequest();
		$this->logger->log($level, $message, [
			'request' => [
				'method' => $request->getMethod(),
				'path' => $request->getPath(),
				'body' => $request->body
			],
			'session' => [
				'player_id' => $this->sessionWrapper->get('playerId')
			],
			'file' => $file,
			'line' => $line
		]);
		
		$this->sessionWrapper->addFlashbag($message, $flashbagLevel);
		
		if ($this->database->inTransaction()) {
			$this->database->rollBack();
		}
		
		$response = new Response();
		$redirectionData = $this->getRedirection($request, $redirect);
		if (isset($redirectionData['redirect'])) {
			$response->redirect($redirectionData['redirect']);
		} else {
			$response->setStatusCode(Response::STATUS_INTERNAL_SERVER_ERROR);
			$response->addTemplate($redirectionData['template']);
		}
		$event->setResponse($response);
	}
	
	/**
	 * @return array<string, string>
	 */
	public function getRedirection(Request $request, string $redirect = null): array
	{
		if ($redirect !== null) {
			return ['redirect' => $redirect];
		}
		
		$history = $this->sessionWrapper->getHistory();

		if (($nbPaths = count($history)) === 0) {
			return ['redirect' => '/'];
		}
		if (($redirect = '/' . $this->sessionWrapper->getLastHistory()) === $request->getPath()) {
			// We get the path before the last one if available
			$redirect = ($nbPaths > 1) ? $history[$nbPaths - 2] : '/';
		}
		// In this case, it means the user is in an error loop
		if ($nbPaths > 3 && $redirect === $history[$nbPaths - 4]) {
			return [
				'template' => $this->templatePath . 'fatal.php'
			];
		}
		return ['redirect' => $redirect];
	}
}
