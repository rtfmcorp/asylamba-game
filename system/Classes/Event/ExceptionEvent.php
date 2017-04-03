<?php

namespace Asylamba\Classes\Event;

use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;

class ExceptionEvent {
	/** @var \Exception **/
	protected $exception;
	/** @var Request **/
	protected $request;
	/** @var Response **/
	protected $response;
	
	const NAME = 'core.exception';
	
	/**
	 * @param Request $request
	 * @param \Exception $exception
	 */
	public function __construct(Request $request, \Exception $exception)
	{
		$this->request = $request;
		$this->exception = $exception;
	}
	
	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}
	
	/**
	 * @return \Exception
	 */
	public function getException()
	{
		return $this->exception;
	}
	
	/**
	 * @param Response $response
	 * @return \Asylamba\Classes\Event\ErrorEvent
	 */
	public function setResponse(Response $response)
	{
		$this->response = $response;
		
		return $this;
	}
	
	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}
}