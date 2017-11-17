<?php

namespace Asylamba\Modules\Demeter\Event;

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Zeus\Model\Player;

class ElectionResultsEvent
{
    /** @var Color **/
    protected $faction;
    /** @var Player **/
    protected $winner;
    
    const NAME = 'demeter.election_results';
    
    /**
     * @param Color $faction
     * @param Player $winner
     */
    public function __construct(Color $faction, Player $winner)
    {
        $this->faction = $faction;
        $this->winner = $winner;
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
    public function getWinner()
    {
        return $this->winner;
    }
}
