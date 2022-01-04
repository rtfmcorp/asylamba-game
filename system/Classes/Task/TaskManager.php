<?php

namespace Asylamba\Classes\Task;

use Asylamba\Classes\Process\LoadBalancer;
use Asylamba\Classes\Process\Process;
use Symfony\Component\DependencyInjection\Container;

class TaskManager
{
    public function __construct(protected Container $container)
    {
    }

	public function createTaskFromData(array $data): Task
	{
		$task = match ($data['type']) {
			Task::TYPE_TECHNICAL => $this->createTechnicalTask($data['manager'], $data['method'], $data['id']),
			Task::TYPE_REALTIME => $this->createRealTimeTask($data['manager'], $data['method'], $data['object_id'], $data['date'], $data['id'], ($data['context'] ?? null)),
			Task::TYPE_CYCLIC => $this->createCyclicTask($data['manager'], $data['method'], $data['id']),
		};
		if (isset($data['estimated_time'])) {
			$task->setEstimatedTime($data['estimated_time']);
		}
		return $task;
	}

    public function createRealTimeTask(string $manager, string $method, int $objectId, string $date, int $id = null, array $context = null): Task
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

    public function createCyclicTask(string $manager, string $method, int $id = null): Task
    {
        return
            (new CyclicTask())
            ->setId((($id !== null) ? $id : $this->generateId()))
            ->setManager($manager)
            ->setMethod($method)
        ;
    }

    public function createTechnicalTask(string $manager, string $method, int $id = null): Task
    {
        return
            (new TechnicalTask())
            ->setId((($id !== null) ? $id : $this->generateId()))
            ->setManager($manager)
            ->setMethod($method)
        ;
    }

    public function perform(Task $task): array
    {
		// Get the manager from the container and then execute the given method with its arguments
		\call_user_func_array(
			[$this->container->get($task->getManager()), $task->getMethod()],
			($task instanceof RealTimeTask) ? [$task->getObjectId()] : []
		);
		return ['success' => true, 'task' => $task];
    }

	public function validateTask(Process $process, array $data): void
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
			$this->container->get(LoadBalancer::class)->storeStats($task);
		}
	}
    
    public function generateId(): string
    {
        return uniqid('process_');
    }
}
