<?php

namespace Asylamba\Classes\Event;

use Asylamba\Classes\Task\Task;

class ProcessErrorEvent
{
    /** @var \Error **/
    protected $error;
    /** @var Task **/
    protected $task;
    
    const NAME = 'core.process_error';
    
    /**
     * @param \Error $error
     * @param Task $task
     */
    public function __construct(\Error $error, $task = null)
    {
        $this->error = $error;
        $this->task = $task;
    }
    
    /**
     * @return \Error
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }
}
