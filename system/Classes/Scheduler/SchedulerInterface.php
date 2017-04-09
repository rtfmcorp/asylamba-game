<?php

namespace Asylamba\Classes\Scheduler;

interface SchedulerInterface
{
	/**
	 * This method is meant to register a new action to schedule
	 * The action is put in the queue to be executed
	 * 
	 * @param string $manager
	 * @param string $method
	 * @param array $arguments
	 */
	public function schedule($manager, $method, $arguments = []);
	
	/**
	 * This method is meant to executed the scheduled data if their date is passed
	 * In case of cyclic actions, the scheduler will check the current hour and compare it to the last executed hour
	 */
	public function execute();
}