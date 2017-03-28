<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Container\Session;

class Client
{
    /** @var string **/
    protected $id;
    /** @var boolean **/
    protected $isFirstConnection;
    /** @var boolean **/
    protected $isAuthenticated;
    /** @var Session **/
    protected $session;
    
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
     * @param boolean $isFirstConnection
     * @return $this
     */
    public function setIsFirstConnection($isFirstConnection)
    {
        $this->isFirstConnection = $isFirstConnection;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsFirstConnection()
    {
        return $this->isFirstConnection;
    }
    
    /**
     * @param Session $session
     * @return $this
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
        
        return $this;
    }
    
    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }
}