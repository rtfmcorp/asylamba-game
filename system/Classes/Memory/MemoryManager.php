<?php

namespace Asylamba\Classes\Memory;

class MemoryManager
{
    /** @var int **/
    private $nodeMemory;
    /** @var int **/
    private $nodeAllocatedMemory;
    /** @var int **/
    protected $poolMemory;
    /** @var int **/
    protected $poolAllocatedMemory;
    
    /**
     * {inheritdoc}
     */
    public function refreshNodeMemory()
    {
        $this->nodeMemory = memory_get_usage();
        $this->nodeAllocatedMemory = memory_get_usage(true);
    }
    
    /**
     * @return int
     */
    public function getNodeMemory()
    {
        return $this->nodeMemory;
    }
    
    /**
     * @return int
     */
    public function getNodeAllocatedMemory()
    {
        return $this->nodeAllocatedMemory;
    }
    
    /**
     * @param int $poolMemory
     */
    public function setPoolMemory($poolMemory)
    {
        $this->poolMemory = $poolMemory;
    }
    
    /**
     * @return int
     */
    public function getPoolMemory()
    {
        return $this->poolMemory;
    }
    
    /**
     * @param int $poolAllocatedMemory
     */
    public function setPoolAllocatedMemory($poolAllocatedMemory)
    {
        $this->poolAllocatedMemory = $poolAllocatedMemory;
    }
    
    /**
     * @return int
     */
    public function getPoolAllocatedMemory()
    {
        return $this->poolAllocatedMemory;
    }
}