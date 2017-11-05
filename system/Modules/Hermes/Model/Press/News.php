<?php

namespace Asylamba\Modules\Hermes\Model\Press;

abstract class News implements \JsonSerializable
{
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $title;
    /** @var string **/
    protected $content;
    /** @var string **/
    protected $type;
    /** @var int **/
    protected $weight;
    /** @var \DateTime **/
    protected $createdAt;
    /** @var \DateTime **/
    protected $updatedAt;
    
    const NEWS_TYPE_MILITARY = 'military';
    const NEWS_TYPE_POLITICS = 'politics';
    const NEWS_TYPE_TRADE = 'trade';
   
    const WEIGHT_LIGHT = 1;
    const WEIGHT_NORMAL = 2;
    const WEIGHT_IMPORTANT = 3;
    const WEIGHT_CRITICAL = 4;
    
    /**
     * @param int $id
     * @return $this
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
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * @param int $weight
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }
    
    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * @return string
     */
    abstract public function getNewsPicto();
    
    /**
     * @return int
     */
    abstract public function getNewsFaction();
    
    /**
     * @return string
     */
    abstract protected function getNewsBanner();
    
    /**
     * @return string
     */
    abstract protected function getNewsType();
    
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->createdAt,
            'news_picto' => $this->getNewsPicto(),
            'news_type' => $this->getNewsType()
        ];
    }
}
