<?php

namespace Asylamba\Classes\Task;

class RealTimeTask extends Task
{
    /** @var int **/
    protected $objectId;
    /** @var string **/
    protected $date;
    /** @var array **/
    protected $context;
    
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_REALTIME;
    }
    
    /**
     * @param int $id
     * @return $this
     */
    public function setObjectId($id)
    {
        $this->objectId = $id;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }
    
    /**
     * @param string $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * @param array $context
     * @return \Asylamba\Classes\Task\RealTimeTask
     */
    public function setContext($context)
    {
        $this->context = $context;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
    
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'object_id' => $this->objectId,
            'date' => $this->date
        ]);
    }
}
