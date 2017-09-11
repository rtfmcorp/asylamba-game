<?php

/**
 * Technology Queue
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 10.02.14
*/
namespace Asylamba\Modules\Promethee\Model;

class TechnologyQueue
{
    /** @var int **/
    public $id;
    /** @var int **/
    public $rPlayer;
    /** @var int **/
    public $rPlace;
    /** @var int **/
    public $technology;
    /** @var int **/
    public $targetLevel;
    /** @var string **/
    public $dStart;
    /** @var string **/
    public $dEnd;

    /**
     * @param int $id
     * @return TechnologyQueue
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
     * @param int $playerId
     * @return TechnologyQueue
     */
    public function setPlayerId($playerId)
    {
        $this->rPlayer = $playerId;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getPlayerId()
    {
        return $this->rPlayer;
    }
    
    /**
     * @param int $placeId
     * @return TechnologyQueue
     */
    public function setPlaceId($placeId)
    {
        $this->rPlace = $placeId;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getPlaceId()
    {
        return $this->rPlace;
    }
    
    /**
     * @param int $technology
     * @return TechnologyQueue
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getTechnology()
    {
        return $this->technology;
    }
    
    /**
     * @param int $targetLevel
     * @return TechnologyQueue
     */
    public function setTargetLevel($targetLevel)
    {
        $this->targetLevel = $targetLevel;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getTargetLevel()
    {
        return $this->targetLevel;
    }
    
    /**
     * @param string $createdAt
     * @return TechnologyQueue
     */
    public function setCreatedAt($createdAt)
    {
        $this->dStart = $createdAt;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->dStart;
    }
    
    /**
     * @param string $endedAt
     * @return TechnologyQueue
     */
    public function setEndedAt($endedAt)
    {
        $this->dEnd = $endedAt;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getEndedAt()
    {
        return $this->dEnd;
    }
}
