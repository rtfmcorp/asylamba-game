<?php

namespace Asylamba\Classes\Task;

use Asylamba\Classes\DependencyInjection\Container;

use Asylamba\Classes\Process\Process;

class TaskManager
{
	/** @var Container **/
	protected $container;
    
	/**
	 * @param Container $container
	 */
    public function __construct(Container $container)
    {
		$this->container = $container;
    }
	
	/**
	 * @param array $data
	 * @return Task
	 */
	public function createTaskFromData($data)
	{
		switch ($data['type']) {
			case Task::TYPE_TECHNICAL:
				$task = $this->createTechnicalTask($data['manager'], $data['method'], $data['id']);
				break;
			case Task::TYPE_REALTIME:
				$task = $this->createRealTimeTask($data['manager'], $data['method'], $data['object_id'], $data['date'], $data['id'], ($data['context'] ?? null));
				break;
			case Task::TYPE_CYCLIC:
				$task = $this->createCyclicTask($data['manager'], $data['method'], $data['id']);
				break;
		}
		if (isset($data['estimated_time'])) {
			$task->setEstimatedTime($data['estimated_time']);
		}
		return $task;
	}
    
    /**
     * @param string $manager
     * @param string $method
     * @param int $objectId
     * @param string $date
	 * @param int $id
	 * @param array $context
     * @return Task
     */
    public function createRealTimeTask($manager, $method, $objectId, $date, $id = null, $context = null)
    {
        return
            (new RealTimeTask())
            ->setId((($id !== null) ? $id : $this->generateId()))
            ->setManager($manager)
            ->setMethod($method)
            ->setObjectId($objectId)
            ->setDate($date)
			->setContext($context)
        ;
    }
    
    /**
     * @param string $manager
     * @param string $method
     * @return Task
     */
    public function createCyclicTask($manager, $method, $id = null)
    {
        return
            (new CyclicTask())
            ->setId((($id !== null) ? $id : $this->generateId()))
            ->setManager($manager)
            ->setMethod($method)
        ;
    }
    
    /**
     * @param string $manager
     * @param string $method
     * @return Task
     */
    public function createTechnicalTask($manager, $method, $id = null)
    {
        return
            (new TechnicalTask())
            ->setId((($id !== null) ? $id : $this->generateId()))
            ->setManager($manager)
            ->setMethod($method)
        ;
    }
    
	/**
	 * @param \Asylamba\Classes\Task\Task $task
	 * @return array
	 */
    public function perform(Task $task)
    {
		// Get the manager from the container and then execute the given method with its arguments
		call_user_func_array(
			[$this->container->get($task->getManager()), $task->getMethod()],
			($task instanceof RealTimeTask) ? [$task->getObjectId()] : []
		);
		return ['success' => true, 'task' => $task];
    }
	
	/**
	 * @param Process $process
	 * @param array $data
	 */
	public function validateTask(Process $process, $data)
	{
		$task = $this->createTaskFromData($data['task']);
		
		if (!isset($data['time'])) {
			$data['time'] = 0.0;
		}
		$task->setTime((float) $data['time']);
		
		$process->removeTask($task);
		if ($task instanceof RealTimeTask && $task->getContext() !== null) {
			$process->removeContext($task->getContext());
		}
		
		if ($data['success'] === true) {
			$this->container->get('load_balancer')->storeStats($task);
		}
	}
    
    public function generateId()
    {
        return uniqid('process_');
    }
}