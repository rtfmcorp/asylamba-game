<?php

namespace Asylamba\Modules\Hermes\Model\Press;

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Gaia\Model\Place;

class MilitaryNews extends News
{
    /** @var Player **/
    protected $attacker;
    /** @var Player **/
    protected $defender;
    /** @var Place **/
    protected $place;
    /** @var string **/
    protected $type;
    /** @var boolean **/
    protected $isVictory;
    
    const TYPE_LOOT = 'loot';
    const TYPE_CONQUEST = 'conquest';
    
    /**
     * @param Player $attacker
     * @return $this
     */
    public function setAttacker(Player $attacker)
    {
        $this->attacker = $attacker;
        
        return $this;
    }
    
    /**
     * @return Player
     */
    public function getAttacker()
    {
        return $this->attacker;
    }
    
    /**
     * @param Player $defender
     * @return $this
     */
    public function setDefender(Player $defender)
    {
        $this->defender = $defender;
        
        return $this;
    }
    
    /**
     * @return Player
     */
    public function getDefender()
    {
        return $this->defender;
    }
    
    /**
     * @param Place $place
     * @return $this
     */
    public function setPlace(Place $place)
    {
        $this->place = $place;
        
        return $this;
    }
    
    /**
     * @return Place
     */
    public function getPlace()
    {
        return $this->place;
    }
    
    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @param boolean $isVictory
     * @return $this
     */
    public function setIsVictory($isVictory)
    {
        $this->isVictory = $isVictory;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsVictory()
    {
        return $this->isVictory;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getNewsType()
    {
        return self::NEWS_TYPE_MILITARY;
    }
    
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'type' => $this->type,
            'attacker' => $this->attacker,
            'defender' => $this->defender,
            'place' => $this->place,
            'is_victory' => $this->isVictory
        ]);
    }
}
