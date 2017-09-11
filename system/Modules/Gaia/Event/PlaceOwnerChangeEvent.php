<?php

namespace Asylamba\Modules\Gaia\Event;

use Asylamba\Modules\Gaia\Model\Place;

class PlaceOwnerChangeEvent
{
    /** @var Place **/
    protected $place;
    
    const NAME = 'gaia.place_owner_change';
    
    /**
     * @param Place $place
     */
    public function __construct(Place $place)
    {
        $this->place = $place;
    }
    
    /**
     * @return Place
     */
    public function getPlace()
    {
        return $this->place;
    }
}
