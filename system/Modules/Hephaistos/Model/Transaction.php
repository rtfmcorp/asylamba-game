<?php

namespace Asylamba\Modules\Hephaistos\Model;

abstract class Transaction implements \JsonSerializable
{
    /** @var int **/
    protected $id;
    /** @var int **/
    protected $amount;
    /** @var \DateTime **/
    protected $createdAt;
    
    const TYPE_DONATION = 'donation';
    const TYPE_CHARGE = 'charge';
    
    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param int $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @return string
     */
    abstract public function getTransactionType();
}