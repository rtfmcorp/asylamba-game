<?php

namespace Asylamba\Classes\Scheduler;

use Asylamba\Classes\DependencyInjection\Container;

class CyclicActionScheduler implements SchedulerInterface
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
		$this->schedule('zeus.player_manager', 'uCredit');
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
			call_user_method_array($action['method'], $this->container->get($action['manager']), $action['arguments']);
		}
		$this->lastExecutedHour = $currentHour;
	}
}