<?php

namespace App\Classes\Event;

use App\Classes\Library\Http\Request;
use App\Classes\Library\Http\Response;

class ExceptionEvent
{
	protected ?Response $response = null;

	public function __construct(protected Request $request, protected \Throwable $throwable)
	{
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	public function getThrowable(): \Throwable
	{
		return $this->throwable;
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