<?php

namespace Asylamba\Classes\Event;

use Asylamba\Classes\Task\Task;

class ProcessExceptionEvent {
	/** @var \Exception **/
	protected $exception;
	/** @var Task **/
	protected $task;
	
	const NAME = 'core.process_exception';
	
	/**
	 * @param \Exception $exception
	 * @param Task $task
	 */
	public function __construct(\Exception $exception, $task = null)
	{
		$this->exception = $exception;
		$this->task = $task;
	}
	
	/**
	 * @return \Exception
	 */
	public function getException()
	{
		return $this->exception;
	}
	
	/**
	 * @return Task
	 */
	public function getTask()
	{
		return $this->task;
	}
}