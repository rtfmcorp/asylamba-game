<?php

namespace Asylamba\Classes\Process;

use Asylamba\Classes\Task\Task;
use Asylamba\Classes\Task\RealTimeTask;
use Symfony\Contracts\Service\Attribute\Required;

class LoadBalancer
{
	protected ProcessManager $processManager;
    protected array $stats = [];

    public function __construct(
		protected int $statsVolume
	) {
    }

	#[Required]
	public function setProcessManager(ProcessManager $processManager): void
	{
		$this->processManager = $processManager;
	}

    public function affectTask(Task $task): bool
    {
        $this->estimateTime($task);
        
        $selectedProcess = $minTime = null;

		if (0 === count($this->processManager->getProcesses())) {
			return false;
		}
        
        foreach ($this->processManager->getProcesses() as $process) {
			// If the process has a task of the same context than the current one, we affect it to the process queue
			if ($task instanceof RealTimeTask && $task->getContext() !== null && $process->hasContext($task->getContext())) {
				$selectedProcess = $process;
				break;
			}
			if ($process->getExpectedWorkTime() < $minTime || $minTime === null) {
				$selectedProcess = $process;
				$minTime = $process->getExpectedWorkTime();
			}
        }
		if (null === $selectedProcess) {
			return false;
		}
        $this->processManager->affectTask($selectedProcess, $task);

		return true;
    }
    
	/**
	 * @param Task $task
	 */
    public function storeStats(Task $task)
    {
        $key = $task->getManager() . '.' . $task->getMethod();
        $this->stats[$key][] = $task->getTime();
        
        if (count($this->stats[$key]) > $this->statsVolume) {
            array_shift($this->stats[$key]);
        }
    }
    
    /**
     * @return array
     */
    public function getStats()
    {
        return $this->stats;
    }
    
    /**
     * @param Task $task
     */
    public function estimateTime(Task $task)
    {
		$key = $task->getManager() . '.' . $task->getMethod();
		
		if (!isset($this->stats[$key])) {
			$task->setEstimatedTime($task::DEFAULT_ESTIMATED_TIME);
			return;
		}
		
        $taskStats = $this->stats[$key];
        $task->setEstimatedTime(array_sum($taskStats) / count($taskStats));
    }
}
