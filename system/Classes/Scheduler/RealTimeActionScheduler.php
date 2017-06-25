<?php

namespace Asylamba\Classes\Scheduler;

use Asylamba\Classes\DependencyInjection\Container;

use Asylamba\Classes\Task\TaskManager;
use Asylamba\Classes\Process\LoadBalancer;
use Asylamba\Classes\Process\ProcessGateway;

class RealTimeActionScheduler
{
	/** @var Container **/
	protected $container;
    /** @var TaskManager **/
    protected $taskManager;
	/** @var LoadBalancer **/
	protected $loadBalancer;
	/** @var ProcessGateway **/
	protected $processGateway;
	/** @var array **/
	protected $queue = [];
	
	/**
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
        $this->taskManager = $container->get('task_manager');
        $this->loadBalancer = $container->get('load_balancer');
        $this->processGateway = $container->get('process_gateway');
	}
	
	public function init()
	{
		$this->container->get('ares.commander_manager')->scheduleMovements();
		$this->container->get('athena.building_queue_manager')->scheduleActions();
		//$this->container->get('athena.recycling_mission_manager')->scheduleActions();
		$this->container->get('athena.ship_queue_manager')->scheduleActions();
		$this->container->get('promethee.technology_queue_manager')->scheduleQueues();
		$factionManager = $this->container->get('demeter.color_manager');
		$factionManager->scheduleSenateUpdate();
		$factionManager->scheduleCampaigns();
		$factionManager->scheduleElections();
		$factionManager->scheduleBallot();
		$this->execute();
	}
	
	/**
	 * This method is meant to register a new action to schedule
	 * The action is put in the queue to be executed
	 * 
	 * @param string $manager
	 * @param string $method
	 * @param array $object
	 * @param string $date
	 * @param array $context
	 */
	public function schedule($manager, $method, $object, $date, $context = null)
	{
		if (P_TYPE === 'worker') {
			return $this->processGateway->writeToMaster([
				'command' => 'schedule',
				'data' => [
					'manager' => $manager,
					'method' => $method,
					'object_class' => get_class($object),
					'object_id' => $object->id,
					'date' => $date,
					'context' => $context 
				]
			]);
		}
		$this->queue[$date][get_class($object) . '-' . $object->id] = $this->taskManager->createRealTimeTask(
			$manager,
			$method,
			$object->id,
			$date,
			null,
			$context
		);
		// Sort the queue by date
		ksort($this->queue);
	}
	
	/**
	 * @param string $manager
	 * @param string $method
	 * @param string $objectClass
	 * @param int $objectId
	 * @param string $date
	 * @param array $context
	 */
	public function scheduleFromProcess($manager, $method, $objectClass, $objectId, $date, $context = null)
	{
		$this->queue[$date][$objectClass . '-' . $objectId] = $this->taskManager->createRealTimeTask($manager, $method, $objectId, $date, null, $context);
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
			foreach ($actions as $task) {
				$this->loadBalancer->affectTask($task);
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