<?php

namespace Asylamba\Modules\Hermes\Model\Press;

use Asylamba\Modules\Demeter\Model\Color;

class PoliticNews extends News
{
    /** @var Color **/
    protected $faction;
    /** @var string **/
    protected $type;
    
    const TYPE_CAMPAIGN = 'campaign';
    const TYPE_CANDIDATE = 'candidate';
    const TYPE_ELECTION = 'election';
    const TYPE_PUTSCH_ATTEMPT = 'putsch-attempt';
    const TYPE_PUTSCH_FAILURE = 'putsch-failure';
    const TYPE_PUTSCH_SUCCESS = 'putsch-success';
    const TYPE_RESULTS = 'results';
    const TYPE_SENATE = 'senate';
    
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
    public function getNewsPicto()
    {
        return [
            self::TYPE_CAMPAIGN => MEDIA . '/faction/nav/forum.png',
            self::TYPE_CANDIDATE => MEDIA . '/faction/data/law.png',
            self::TYPE_ELECTION => MEDIA . '/faction/nav/government.png',
            self::TYPE_PUTSCH_ATTEMPT => MEDIA . '/faction/nav/election.png',
            self::TYPE_PUTSCH_FAILURE => MEDIA . '/fleet/movement.png',
            self::TYPE_PUTSCH_SUCCESS => MEDIA . '/faction/nav/overview.png',
            self::TYPE_RESULTS => MEDIA . '/faction/nav/overview.png',
            self::TYPE_SENATE => MEDIA. '/faction/law/common.png'
        ][$this->type];
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getNewsType()
    {
        return self::NEWS_TYPE_POLITICS;
    }
    
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'type' => $this->type,
            'faction' => $this->faction
        ]);
    }
}
