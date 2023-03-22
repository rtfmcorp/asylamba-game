<?php

namespace Asylamba\Classes\Event;

use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;

class ExceptionEvent
{
	protected ?Response $response = null;
	
	const NAME = 'core.exception';

	public function __construct(protected Request $request, protected \Exception $exception)
	{
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	public function getException(): \Exception
	{
		return $this->exception;
	}

	public function setResponse(Response $response): void
	{
		$this->response = $response;
	}

	public function getResponse(): Response
	{
		return $this->response;
	}
}
