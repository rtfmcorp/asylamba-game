<?php

namespace Asylamba\Classes\Process;

use Asylamba\Classes\Task\Task;

class Process
{
    /** @var string **/
    protected $name;
    /** @var integer **/
    protected $memory;
    /** @var integer **/
    protected $allocatedMemory;
    /** @var \DateTime **/
    protected $startTime;
    /** @var resource **/
    protected $input;
    /** @var resource **/
    protected $output;
    /** @var resource **/
    protected $process;
    /** @var Tasks **/
    protected $tasks;
	/** @var array **/
	protected $contexts;
	/** @var float **/
	protected $expectedWorkTime = 0.0;
    
    public function __construct()
    {
        $this->startTime = new \DateTime();
        $this->tasks = [];
        $this->contexts = [];
    }
    
    /**
     * @param integer $memory
     * @return Process
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;
        
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getMemory()
    {
        return $this->memory;
    }
    
    /**
     * @param integer $allocatedMemory
     * @return Process
     */
    public function setAllocatedMemory($allocatedMemory)
    {
        $this->allocatedMemory = $allocatedMemory;
        
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getAllocatedMemory()
    {
        return $this->allocatedMemory;
    }
    
    /**
     * @return integer
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    /**
     * Set the Process input
     * 
     * @param resource $input
     * @return Process
     */
    public function setInput($input)
    {
        $this->input = $input;
        
        return $this;
    }
    
    /**
     * @return resource
     */
    public function getInput()
    {
        return $this->input;
    }
    
    /**
     * Set the Process output
     * 
     * @param resource $output
     * @return Process
     */
    public function setOutput($output)
    {
        $this->output = $output;
        
        return $this;
    }
    
    /**
     * @return resource
     */
    public function getOutput()
    {
        return $this->output;
    }
    
    /**
     * Set name
     * 
     * @param string $name
     * @return Process
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set process
     * 
     * @param resource $process
     * @return Process
     */
    public function setProcess($process)
    {
        $this->process = $process;
        
        return $this;
    }
    
    /**
     * @return resource
     */
    public function getProcess()
    {
        return $this->process;
    }
    
	/**
	 * @param Task $task
	 * @return \Asylamba\Classes\Process\Process
	 */
    public function addTask(Task $task)
    {
        $this->tasks[$task->getId()] = $task;
        
        return $this;
    }
    
    /**
     * @param Task $task
	 * @return \Asylamba\Classes\Process\Process
     */
    public function removeTask(Task $task)
    {
		$this->expectedWorkTime -= $task->getEstimatedTime();
		if ($this->expectedWorkTime < 0) {
			$this->expectedWorkTime = 0.0;
		}

		unset($this->tasks[$task->getId()]);
		
		return $this;
    }
    
	/**
	 * @return array
	 */
    public function getTasks()
    {
        return $this->tasks;
    }
	
	/**
	 * @param array $context
	 * @return \Asylamba\Classes\Process\Process
	 */
	public function addContext($context)
	{
		if (!$this->hasContext($context)) {
			$this->contexts[$context['class'] . '-' . $context['id']] = 0;
		}
		$this->contexts[$context['class'] . '-' . $context['id']]++;
		return $this;
	}
	
	/**
	 * @param array $context
	 * @return boolean
	 */
	public function hasContext($context)
	{
		return isset($this->contexts[$context['class'] . '-' . $context['id']]);
	}
	
	/**
	 * @param array $context
	 * @return \Asylamba\Classes\Process\Process
	 */
	public function removeContext($context)
	{
		$this->contexts[$context['class'] . '-' . $context['id']]--;
		if ($this->contexts[$context['class'] . '-' . $context['id']] === 0) {
			unset($this->contexts[$context['class'] . '-' . $context['id']]);
		}
		return $this;
	}
	
	/**
	 * @param float $expectedWorkTime
	 */
	public function setExpectedWorkTime($expectedWorkTime)
	{
		$this->expectedWorkTime = $expectedWorkTime;
        
        return $this;
	}
	
	/**
	 * @return float
	 */
	public function getExpectedWorkTime()
	{
		return $this->expectedWorkTime;
	}
}