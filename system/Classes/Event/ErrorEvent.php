<?php

namespace Asylamba\Classes\Event;

class ErrorEvent {
	/** @var \Error **/
	protected $error;
	
	const NAME = 'core.error';
	
	/**
	 * @param \Error $error
	 */
	public function __construct(\Error $error)
	{
		$this->error = $error;
	}
	
	/**
	 * @return \Error
	 */
	public function getError()
	{
		return $this->error;
	}
}