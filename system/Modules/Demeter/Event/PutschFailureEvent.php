<?php

namespace Asylamba\Modules\Demeter\Event;

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Zeus\Model\Player;

class PutschFailureEvent
{
    /** @var Color **/
    protected $faction;
    /** @var Player **/
    protected $leader;
    /** @var Player **/
    protected $pretender;
    
    const NAME = 'demeter.putsch_failure';
    
    /**
     * @param Color $faction
     * @param Player $leader
     * @param Player $pretender
     */
    public function __construct(Color $faction, Player $leader = null, Player $pretender)
    {
        $this->faction = $faction;
        $this->leader = $leader;
        $this->pretender = $pretender;
    }
    
    /**
     * @return Color
     */
    public function getFaction()
    {
        return $this->faction;
    }
    
    /**
     * @return Player
     */
    public function getLeader()
    {
        return $this->leader;
    }
    
    /**
     * @return Player
     */
    public function getPretender()
    {
        return $this->pretender;
    }
}
