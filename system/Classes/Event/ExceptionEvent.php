<?php

namespace Asylamba\Classes\Event;

class ExceptionEvent {
	/** @var \Exception **/
	protected $exception;
	
	const NAME = 'core.exception';
	
	/**
	 * @param \Exception $exception
	 */
	public function __construct(\Exception $exception)
	{
		$this->exception = $exception;
	}
	
	/**
	 * @return \Exception
	 */
	public function getException()
	{
		return $this->exception;
	}
}