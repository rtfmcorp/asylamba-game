<?php

namespace Asylamba\Modules\Demeter\Event;

use Asylamba\Modules\Demeter\Model\Color;

class SenateElectionEvent
{
    /** @var Color **/
    protected $faction;
    
    const NAME = 'demeter.senate_election';
    
    /**
     * @param Color $faction
     */
    public function __construct(Color $faction)
    {
        $this->faction = $faction;
    }
    
    /**
     * @return Color
     */
    public function getFaction()
    {
        return $this->faction;
    }
}
