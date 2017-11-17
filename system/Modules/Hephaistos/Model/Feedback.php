<?php

namespace Asylamba\Modules\Hephaistos\Model;

abstract class Feedback
{
    /** @var string **/
    protected $id;
    /** @var string **/
    protected $title;
    /** @var string **/
    protected $description;
    /** @var array **/
    protected $author;
    /** @var string **/
    protected $status;
    /** @var array **/
    protected $commentaries = [];
    /** @var DateTime **/
    protected $createdAt;
    /** @var DateTime **/
    protected $updatedAt;
    
    const TYPE_BUG = 'bug';
    const TYPE_EVOLUTION = 'evo';
    
    const STATUS_TODO = 'todo';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_TO_VALIDATE = 'to_validate';
    const STATUS_DONE = 'done';
    
    /**
     * @return string
     */
    abstract public function getType();
    
    /**
     * @param string $id
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
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * @param string $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * @param mixed $commentary
     * @return $this
     */
    public function addCommentary($commentary)
    {
        $this->commentaries[] = $commentary;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getCommentaries()
    {
        return $this->commentaries;
    }
    
    /**
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
    
    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @param DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
    
    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}