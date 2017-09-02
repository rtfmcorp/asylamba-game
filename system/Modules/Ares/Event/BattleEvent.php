<?php

namespace Asylamba\Modules\Ares\Event;

use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Zeus\Model\Player;

class BattleEvent
{
    /** @var Commander **/
    protected $commander;
    /** @var Place **/
    protected $place;
    /** @var Player **/
    protected $attacker;
    /** @var Defender **/
    protected $defender;
    /** @var boolean **/
    protected $isVictory;
    
    /**
     * @param Commander $commander
     * @param Player $attacker
     * @param Player $defender
     * @param Place $place
     * @param boolean $isVictory
     */
    public function __construct(Commander $commander, Player $attacker, Player $defender, Place $place, $isVictory)
    {
        $this->commander = $commander;
        $this->attacker = $attacker;
        $this->defender = $defender;
        $this->place = $place;
        $this->isVictory = $isVictory;
    }
    
    /**
     * @return Commander
     */
    public function getCommander()
    {
        return $this->commander;
    }
    
    /**
     * @return Place
     */
    public function getPlace()
    {
        return $this->place;
    }
    
    /**
     * @return Player
     */
    public function getAttacker()
    {
        return $this->attacker;
    }
    
    /**
     * @return Player
     */
    public function getDefender()
    {
        return $this->defender;
    }
    
    /**
     * @return boolean
     */
    public function getIsVictory()
    {
        return $this->isVictory;
    }
}