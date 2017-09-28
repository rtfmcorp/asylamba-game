<?php

namespace Asylamba\Modules\Hephaistos\Model;

use Asylamba\Modules\Zeus\Model\Player;

class Donation
{
    /** @var int **/
    protected $id;
    /** @var Player **/
    protected $player;
    /** @var string **/
    protected $token;
    /** @var int **/
    protected $amount;
    /** @var \DateTime **/
    protected $createdAt;
    
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
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;
        
        return $this;
    }
    
    /**
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }
    
    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
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
}