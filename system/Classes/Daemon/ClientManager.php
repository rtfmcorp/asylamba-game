<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Daemon\Client;

use Asylamba\Classes\Container\Session;

use Asylamba\Classes\Library\Http\Request;

class ClientManager
{
    /** @var array **/
    public $clients = [];
    
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
}