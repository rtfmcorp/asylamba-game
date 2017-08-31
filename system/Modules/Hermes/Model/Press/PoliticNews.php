<?php

namespace Asylamba\Modules\Hermes\Model\Press;

use Asylamba\Modules\Demeter\Model\Color;

class PoliticNews extends News
{
    /** @var Color **/
    protected $faction;
    /** @var string **/
    protected $type;
    
    const TYPE_SENATE = 'senate';
    const TYPE_PUTSCH_ATTEMPT = 'putsch-attempt';
    const TYPE_PUTSCH_FAIL = 'putsch-fail';
    const TYPE_PUTSCH_SUCCESS = 'putsch-success';
    const TYPE_CAMPAIGN = 'campaign';
    const TYPE_ELECTION = 'election';
    const TYPE_RESULTS = 'results';
    
    /**
     * @param Color $faction
     * @return $this
     */
    public function setFaction(Color $faction)
    {
        $this->faction = $faction;
        
        return $this;
    }
    
    /**
     * @return Color
     */
    public function getFaction()
    {
        return $this->faction;
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
     * {@inheritdoc}
     */
    protected function getNewsType()
    {
        return self::NEWS_TYPE_POLITICS;
    }
}
