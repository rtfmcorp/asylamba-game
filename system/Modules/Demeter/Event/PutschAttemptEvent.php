<?php

namespace Asylamba\Modules\Demeter\Event;

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Demeter\Model\Election\Candidate;

class PutschAttemptEvent
{
    /** @var Color **/
    protected $faction;
    /** @var Player **/
    protected $leader;
    /** @var Candidate **/
    protected $pretender;
    
    const NAME = 'demeter.putsch_attempt';
    
    /**
     * @param Color $faction
     * @param Player $leader
     * @param Candidate $pretender
     */
    public function __construct(Color $faction, Player $leader = null, Candidate $pretender)
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
     * @return Candidate
     */
    public function getPretender()
    {
        return $this->pretender;
    }
}
