<?php

namespace Asylamba\Modules\Demeter\Event;

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Model\Election\Candidate;

class CandidateEvent
{
    /** @var Color **/
    protected $faction;
    /** @var Candidate **/
    protected $candidate;
    
    const NAME = 'demeter.candidate';
    
    /**
     * @param Color $faction
     * @param Candidate $candidate
     */
    public function __construct(Color $faction, Candidate $candidate)
    {
        $this->faction = $faction;
        $this->candidate = $candidate;
    }
    
    /**
     * @return Color
     */
    public function getFaction()
    {
        return $this->faction;
    }
    
    /**
     * @return Candidate
     */
    public function getCandidate()
    {
        return $this->candidate;
    }
}
