<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Library\WS\Connection;

class Client
{
    /** @var string **/
    protected $id;
	/** @var \DateTime **/
	protected $lastConnectedAt;
    /** @var boolean **/
    protected $isFirstConnection;
	/** @var int **/
	protected $playerId;
    /** @var Connection **/
    protected $wsConnection;
    
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
	 * @param int $playerId
	 * @return Client
	 */
	public function setPlayerId($playerId)
	{
		$this->playerId = $playerId;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getPlayerId()
	{
		return $this->playerId;
	}
    
    /**
     * @param Connection $connection
     * @return $this
     */
    public function setWsConnection(Connection $connection)
    {
        $this->wsConnection = $connection;
        
        return $this;
    }
    
    public function removeWsConnection()
    {
        $this->wsConnection = null;
    }
    
    /**
     * @return Connection
     */
    public function getWsConnection()
    {
        return $this->wsConnection;
    }
}