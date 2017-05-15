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
				return $this->createTechnicalTask($data['manager'], $data['method'], $data['id']);
			case Task::TYPE_REALTIME:
				return $this->createRealTimeTask($data['manager'], $data['method'], $data['object_id'], $data['date'], $data['id']);
			case Task::TYPE_CYCLIC:
				return $this->createCyclicTask($data['manager'], $data['method'], $data['id']);
		}
	}
    
    /**
     * @param string $manager
     * @param string $method
     * @param int $objectId
     * @param string $date
     * @return Task
     */
    public function createRealTimeTask($manager, $method, $objectId, $date, $id = null)
    {
        return
            (new RealTimeTask())
            ->setId((($id !== null) ? $id : $this->generateId()))
            ->setManager($manager)
            ->setMethod($method)
            ->setObjectId($objectId)
            ->setDate($date)
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
		if ($data['success'] === false) {
			\Asylamba\Classes\Daemon\Server::debug('The task failed : ' . $data['task']['manager'] . '.' . $data['task']['method']);
		}
		$task = $this->createTaskFromData($data['task']);
		
		if (!isset($data['time'])) {
			$data['time'] = 0.0;
		}
		$task->setTime((float) $data['time']);
		
		$process->removeTask($task);
		
		$this->container->get('load_balancer')->storeStats($task);
	}
	
	public function scheduleFromProcess()
	{
		
	}
    
    public function generateId()
    {
        return uniqid('process_');
    }
}