<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Container\Session;

class Client
{
    /** @var string **/
    protected $id;
	/** @var \DateTime **/
	protected $lastConnectedAt;
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
	 * @param \DateTime $lastConnectedAt
	 * @return \Asylamba\Classes\Daemon\Client
	 */
	public function setLastConnectedAt(\DateTime $lastConnectedAt)
	{
		$this->lastConnectedAt = $lastConnectedAt;
		
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getLastConnectedAt()
	{
		return $this->lastConnectedAt;
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