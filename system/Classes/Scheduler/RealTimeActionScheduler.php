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
		$this->container->get('athena.ship_queue_manager')->scheduleActions();
		//$this->container->get('promethee.technology_queue_manager')->scheduleActions();
		$factionManager = $this->container->get('demeter.color_manager');
		$factionManager->scheduleCampaigns();
		$factionManager->scheduleElections();
		$factionManager->scheduleBallot();
	}
	
	/**
	 * This method is meant to register a new action to schedule
	 * The action is put in the queue to be executed
	 * 
	 * @param string $manager
	 * @param string $method
	 * @param array $object
	 * @param string $date
	 */
	public function schedule($manager, $method, $object, $date)
	{
		$this->queue[$date][get_class($object) . '-' . $object->id] = [
			'manager' => $manager,
			'method' => $method,
			'object_id' => $object->id
		];
		// Sort the queue by date
		ksort($this->queue);
	}
	
	/**
	 * This method is meant to executed the scheduled data if their date is passed
	 * In case of cyclic actions, the scheduler will check the current hour and compare it to the last executed hour
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
				call_user_func_array([$this->container->get($action['manager']), $action['method']], [$action['object_id']]);
			}
			unset($this->queue[$date]);
		}
	}
	
	/**
	 * @param object $object
	 * @param string $date
	 * @param string $oldDate
	 */
	public function reschedule($object, $date, $oldDate) {
		$this->queue[$date][get_class($object) . '-' . $object->id] = $this->queue[$oldDate][get_class($object) . '-' . $object->id];
		
		$this->cancel($object, $oldDate);
	}
	
	/**
	 * @param object $object
	 * @param string $date
	 */
	public function cancel($object, $date)
	{
		unset($this->queue[$date][get_class($object) . '-' . $object->id]);
		
		if (empty($this->queue[$date])) {
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