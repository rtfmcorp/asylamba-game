<?php

namespace Asylamba\Classes\Process;

use Asylamba\Classes\Task\Task;
use Asylamba\Classes\Task\RealTimeTask;
use Asylamba\Classes\DependencyInjection\Container;

class LoadBalancer
{
    /** @var Container **/
    protected $container;
    /** @var int **/
    protected $statsVolume;
    /** @var array **/
    protected $stats = [];
    
    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->statsVolume = $container->getParameter('worker_stats_volume');
    }
    
    /**
     * @param Task $task
     */
    public function affectTask(Task $task)
    {
        $this->estimateTime($task);
        
        $selectedProcess = $minTime = null;
        
        $processManager = $this->container->get('process_manager');
        
        foreach ($processManager->getProcesses() as $process) {
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
        $processManager->affectTask($selectedProcess, $task);
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
