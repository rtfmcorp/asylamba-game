<?php

namespace Asylamba\Classes\Event;

class ProcessErrorEvent {
	/** @var \Error **/
	protected $error;
	
	const NAME = 'core.process_error';
	
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