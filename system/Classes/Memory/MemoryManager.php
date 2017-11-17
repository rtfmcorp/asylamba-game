<?php

namespace Asylamba\Classes\Memory;

class MemoryManager
{
    /** @var int **/
    protected $nodeMemory;
    /** @var int **/
    protected $nodeAllocatedMemory;
    /** @var int **/
    protected $poolMemory;
    /** @var int **/
    protected $poolAllocatedMemory;
    /** @var bool **/
    protected $isUpdated;
    
    public function refreshPoolMemory($processes)
    {
        $this->poolMemory = memory_get_usage();
        $this->poolAllocatedMemory = memory_get_usage(true);
        
        foreach ($processes as $process) {
            $this->poolMemory += $process->getMemory();
            $this->poolAllocatedMemory += $process->getAllocatedMemory();
        }
    }
    
    /**
     * {inheritdoc}
     */
    public function refreshNodeMemory()
    {
        $this->isUpdated = true;
        $this->nodeMemory = memory_get_usage();
        $this->nodeAllocatedMemory = memory_get_usage(true);
    }
    
    public function getNodeMemory()
    {
        $data = [
            'memory' => $this->nodeMemory,
            'allocated_memory' => $this->nodeAllocatedMemory,
            'is_updated' => $this->isUpdated
        ];
        $this->isUpdated = false;
        return $data;
    }
    
    public function getPoolMemory()
    {
        return [
            'memory' => $this->poolMemory,
            'allocated_memory' => $this->poolAllocatedMemory
        ];
    }
}
