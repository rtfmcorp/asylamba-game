<?php

namespace Asylamba\Modules\Hephaistos\Model;

use Asylamba\Modules\Zeus\Model\Player;

class Donation extends Transaction
{
    /** @var Player **/
    protected $player;
    /** @var string **/
    protected $token;
    
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
    
    public function getTransactionType()
    {
        return self::TYPE_DONATION;
    }
    
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'transaction_type' => $this->getTransactionType(),
            'player' => $this->player,
            'created_at' => $this->createdAt
            
        ];
    }
}