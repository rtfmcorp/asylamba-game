<?php

namespace Asylamba\Classes\Task;

abstract class Task implements \JsonSerializable
{
    protected string $id;
    protected string $manager;
    protected string $method;
    protected float $estimatedTime;
    protected float $time;
    
    const TYPE_TECHNICAL = 'technical';
    const TYPE_REALTIME = 'realtime';
    const TYPE_CYCLIC = 'cyclic';
    
	const DEFAULT_ESTIMATED_TIME = 1.0;
	
	/**
	 * @return string
	 */
	abstract public function getType();
    
    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param string $manager
     * @return $this
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getManager()
    {
        return $this->manager;
    }
    
    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * @param float $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;
        
        return $this;
    }
    
    /**
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }
    
    /**
     * @param float $estimatedTime
     * @return $this
     */
    public function setEstimatedTime($estimatedTime)
    {
        $this->estimatedTime = $estimatedTime;
        
        return $this;
    }
    
    /**
     * @return float
     */
    public function getEstimatedTime()
    {
        return $this->estimatedTime;
    }
	
	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'type' => $this->getType(),
			'manager' => $this->manager,
			'method' => $this->method,
			'estimated_time' => $this->estimatedTime
		];
	}
}
