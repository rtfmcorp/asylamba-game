<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Daemon\Client;

use Asylamba\Classes\Container\Session;

use Asylamba\Classes\Library\Http\Request;

class ClientManager
{
    /** @var array **/
    public $clients = [];
    /** @var int **/
	protected $sessionLifetime;
	
	/**
	 * @param int $sessionLifetime
	 */
	public function __construct($sessionLifetime)
	{
		$this->sessionLifetime = $sessionLifetime;
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
        return $client;
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
		$session = new Session();
		$session->add('session_id', $client->getId());
        $client->setSession($session);
        
        $this->clients[$client->getId()] = $client;
        return $client;
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