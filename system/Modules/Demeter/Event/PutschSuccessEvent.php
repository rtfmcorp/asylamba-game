<?php

namespace Asylamba\Modules\Demeter\Event;

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Zeus\Model\Player;

class PutschSuccessEvent
{
    /** @var Color **/
    protected $faction;
    /** @var Player **/
    protected $newLeader;
    /** @var Player **/
    protected $previousLeader;
    
    const NAME = 'demeter.putsch_success';
    
    /**
     * @param Color $faction
     * @param Player $newLeader
     * @param Player $previousLeader
     */
    public function __construct(Color $faction, Player $newLeader, Player $previousLeader = null)
    {
        $this->faction = $faction;
        $this->newLeader = $newLeader;
        $this->previousLeader = $previousLeader;
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
    public function getNewLeader()
    {
        return $this->newLeader;
    }
    
    /**
     * @return Player
     */
    public function getPreviousLeader()
    {
        return $this->previousLeader;
    }
}
