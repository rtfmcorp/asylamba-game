<?php

namespace Asylamba\Classes\Process;

use Asylamba\Classes\Task\Task;
use Asylamba\Classes\Task\RealTimeTask;
use Asylamba\Classes\Daemon\Server;
use Asylamba\Classes\Memory\MemoryManager;

class ProcessManager
{
    /** @var Server **/
    protected $server;
    /** @var MemoryManager **/
    protected $memoryManager;
    /** @var ProcessGateway **/
    protected $gateway;
    /** @var array **/
    private $processes = [];
    /** @var string **/
    private $rootPath;
    /** @var int **/
    protected $scale;
    /** @var int **/
    private $instanciedProcesses = 0;
    
    /**
     * @param Server $server
     * @param MemoryManager $memoryManager
     * @param ProcessGateway $gateway
     * @param string $rootPath
     * @param int $scale
     */
    public function __construct(Server $server, MemoryManager $memoryManager, ProcessGateway $gateway, $rootPath, $scale)
    {
        $this->server = $server;
        $this->memoryManager = $memoryManager;
        $this->gateway = $gateway;
        $this->rootPath = $rootPath;
        $this->scale = $scale;
    }
    
    public function __destruct()
    {
        foreach($this->processes as $process)
        {
            $this->removeProcess($process->getName(), "{$process->getName()} shutdown");
        }
    }
	
	/**
	 * @param string $name
	 * @return Process
	 */
	public function getByName($name)
	{
		return $this->processes[$name];
	}
	
	public function affectTask(Process $process, Task $task)
	{
		$process->addTask($task);
		$process->setExpectedWorkTime($process->getExpectedWorkTime() + (float) $task->getEstimatedTime());
		
		if ($task instanceof RealTimeTask && $task->getContext() !== null) {
			$process->addContext($task->getContext());
		}
		$this->getGateway()->writeTo($process, $task);
	}
    
    public function launchProcesses()
    {
        for ($i = 0; $i < $this->scale; $i++) {
            $this->addProcess();
        }
    }
    
    /**
     * @return array<Process>
     */
    public function getProcesses()
    {
        return $this->processes;
    }
    
    /**
     * @return Process
     */
    public function addProcess()
    {
        ++$this->instanciedProcesses;
        $name = "process_{$this->instanciedProcesses}";
        
        $process = proc_open("php {$this->rootPath}/worker.php --process=$name", [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => [
                'file',
                "/srv/logs/proc/$name.error.log",
                'a+'
            ]
        ], $pipes);
		
		stream_set_blocking($pipes[1], 0);
		
        $this->processes[$name] =
            (new Process())
            ->setName($name)
            ->setProcess($process)
            ->setInput($pipes[1])
            ->setOutput($pipes[0])
        ;
        
        $this->server->addInput($name, $this->processes[$name]->getInput());
        
		return $this->processes[$name];
    }
    
    /**
     * Delete a process
     * The reason is the message which will appear in the logs
     * 
     * @param string $name
     * @param string $reason
     * @throws \InvalidArgumentException
     */
    public function removeProcess($name, $reason, $shutdown = false)
    {
        if(!isset($this->processes[$name]))
        {
            throw new \InvalidArgumentException("The given process $name doesn't exist.");
        }
        if($shutdown)
        {
            $this->shutdownProcess($this->processes[$name]);
        }
        $this->server->removeInput($name);
        
        fclose($this->processes[$name]->getInput());
        fclose($this->processes[$name]->getOutput());
        proc_close($this->processes[$name]->getProcess());
        unset($this->processes[$name]);
    }
    
    /**
     * Shutdown a running process
     * 
     * @param Process $process
     */
    public function shutdownProcess(Process $process)
    {
        $this->getGateway()->writeTo($process, [
            'command' => 'shutdown'
        ]);
    }
    
    /**
     * The purpose of this method is to ease tests
     * 
     * @return ProcessGateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }
}