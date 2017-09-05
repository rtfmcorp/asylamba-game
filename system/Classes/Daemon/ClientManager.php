<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Daemon\Client;

use Asylamba\Classes\Redis\RedisManager;
use Asylamba\Classes\Library\Session\SessionWrapper;

use Asylamba\Classes\Library\Http\Request;

use Asylamba\Classes\Library\WS\Connection;

class ClientManager
{
    /** @var array **/
    protected $clients = [];
	/** @var RedisManager **/
	protected $redisManager;
	/** @var SessionWrapper **/
	protected $sessionWrapper;
    /** @var int **/
	protected $sessionLifetime;
	
	/**
	 * @param RedisManager $redisManager
	 * @param SessionWrapper $sessionWrapper
	 * @param int $sessionLifetime
	 */
	public function __construct(RedisManager $redisManager, SessionWrapper $sessionWrapper, $sessionLifetime)
	{
		$this->redisManager = $redisManager;
		$this->sessionWrapper = $sessionWrapper;
		$this->sessionLifetime = $sessionLifetime;
	}
	
	/**
	 * @return array
	 */
	public function getClients()
	{
		return $this->clients;
	}
	
    /**
     * @param Request $request
     * @return Client
     */
    public function getClient(Request $request)
    {
        if (!$request->cookies->exist('session_id')) {
            return null;
        }
        $sessionId = $request->cookies->get('session_id');
        if (!isset($this->clients[$sessionId])) {
            return null;
        }
        $client = $this->clients[$sessionId];
        $client->setIsFirstConnection(false);
        $client->setLastConnectedAt(new \DateTime());
		if (($session = $this->sessionWrapper->fetchSession($sessionId)) === null) {
			return null;
		}
		$this->sessionWrapper->setCurrentSession($session);
        return $client;
    }
    
    public function getClientById($clientId)
    {
        if (!isset($this->clients[$clientId])) {
            return null;
        }
        return $this->clients[$clientId];
    }
    
    /**
     * @param Request $request
     * @return Client
     */
    public function createClient(Request $request)
    {
        $client = 
            (new Client())
            ->setId($this->generateClientId())
            ->setIsFirstConnection(true)
			->setLastConnectedAt(new \DateTime())
        ;
        $this->clients[$client->getId()] = $client;
		$this->sessionWrapper->setCurrentSession($this->sessionWrapper->createSession($client->getId()));
        return $client;
    }
    
    public function broadcast($payload)
    {
        foreach($this->clients as $client) {
            if (($connection = $client->getWsConnection()) !== null) {
                if (!$connection->send($payload)) {
                    $client->removeWsConnection();
                }
            }
        }
    }
    
    public function assignWsConnection(Client $client, $input)
    {
        $client->setWsConnection(new Connection($input));
    }
	
	/**
	 * @param string $sessionId
	 * @param int $playerId
	 */
	public function bindPlayerId($sessionId, $playerId)
	{
		$this->clients[$sessionId]->setPlayerId($playerId);
		
		$this->redisManager->getConnection()->set('player:' . $playerId, $sessionId);
	}
	
	/**
	 * @param int $playerId
	 * @return Session
	 */
	public function getSessionByPlayerId($playerId)
	{
		if (($sessionId = $this->redisManager->getConnection()->get('player:' . $playerId)) === false) {
			return null;
		}
		return $this->sessionWrapper->fetchSession($sessionId);
	}
	
	/**
	 * @return string
	 */
	protected function generateClientId()
	{
		do {
			$id = uniqid();
		} while (isset($this->clients[$id]));
		
		return $id;
	}
	
	/**
	 * @param int $clientId
	 * @return boolean
	 */
	public function removeClient($clientId)
	{
		if (!isset($this->clients[$clientId])) {
			return false;
		}
		$this->redisManager->getConnection()->delete('player:' . $this->clients[$clientId]->getPlayerId());
		unset($this->clients[$clientId]);
		return true;
	}
	
	public function clear()
	{
		$limitDatetime = (new \DateTime("-{$this->sessionLifetime} seconds"));
		
		foreach ($this->clients as $client)
		{
			if ($client->getLastConnectedAt() < $limitDatetime) {
				$this->removeClient($client->getId());
			}
		}
	}
}