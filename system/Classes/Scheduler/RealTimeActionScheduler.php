<?php

namespace Asylamba\Classes\Scheduler;

use Asylamba\Classes\DependencyInjection\Container;

class RealTimeActionScheduler
{
	/** @var Container **/
	protected $container;
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
		$this->container->get('athena.building_queue_manager')->scheduleActions();
		//$this->container->get('athena.recycling_mission_manager')->scheduleActions();
		//$this->container->get('athena.ship_queue_manager')->scheduleActions();
		//$this->container->get('promethee.technology_queue_manager')->scheduleActions();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function schedule($manager, $method, $date, $arguments = [])
	{
		$this->queue[$date][] = [
			'manager' => $manager,
			'method' => $method,
			'arguments' => $arguments
		];
		// Sort the queue by date
		ksort($this->queue);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$now = new \DateTime();
		
		foreach ($this->queue as $date => $actions) {
			// If the action is to be executed later, we break the loop
			// This logic depends on the fact that the queue is key-sorted by date
			if ($now < new \DateTime($date)) {
				break;
			}
			foreach ($actions as $action) {
				// Get the manager from the container and then execute the given method with its arguments
				call_user_func_array([$this->container->get($action['manager']), $action['method']], $action['arguments']);
			}
			unset($this->queue[$date]);
		}
	}
	
	/**
	 * @return array
	 */
	public function getQueue()
	{
		return $this->queue;
	}
}