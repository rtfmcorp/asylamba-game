<?php

namespace Asylamba\Classes\Scheduler;

use Asylamba\Classes\DependencyInjection\Container;

class CyclicActionScheduler
{
	/** @var Container **/
	protected $container;
	/** @var int **/
	protected $lastExecutedHour;
	/** @var array **/
	protected $queue = [];
	
	/**
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}
	
	public function init()
	{
		//$this->schedule('zeus.player_manager', 'uCredit');
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function schedule($manager, $method, $arguments = []) {
		$this->queue[] = [
			'manager' => $manager,
			'method' => $method,
			'arguments' => $arguments
		];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		if (($currentHour = date('H')) === $this->lastExecutedHour) {
			return;
		}
		foreach ($this->queue as $action) {
			// Get the manager from the container and then execute the given method with its arguments
			call_user_func_array([$this->container->get($action['manager']), $action['method']], $action['arguments']);
		}
		$this->lastExecutedHour = $currentHour;
	}
	
	/**
	 * @return array
	 */
	public function getQueue()
	{
		return $this->queue;
	}
}