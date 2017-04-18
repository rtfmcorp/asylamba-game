<?php

namespace Asylamba\Classes\Library\Session;

class SessionWrapper
{
	/** @var Session **/
	protected $currentSession;
	
	public function setCurrentSession(Session $session)
	{
		$this->currentSession = $session;
	}
	
	public function getCurrentSession()
	{
		return $this->currentSession;
	}
	
	public function add($key, $value)
	{
		return $this->currentSession->add($key, $value);
	}
	
	public function addBase($key, $id, $name, $sector, $system, $img, $type)
	{
		return $this->currentSession->addBase($key, $id, $name, $sector, $system, $img, $type);
	}
	
	public function addFlashbag($message, $type)
	{
		return $this->currentSession->addFlashbag($message, $type);
	}
	
	public function addHistory($path)
	{
		return $this->currentSession->addHistory($path);
	}
	
	public function all()
	{
		return $this->currentSession->all();
	}
	
	public function baseExist($id)
	{
		return $this->currentSession->baseExist($id);
	}
	
	public function clear()
	{
		return $this->currentSession->clear();
	}
	
	public function destroy()
	{
		return $this->currentSession->destroy();
	}
	
	public function exist($key)
	{
		return $this->currentSession->exist($key);
	}
	
	public function flushFlashbags()
	{
		return $this->currentSession->flushFlashbags();
	}
	
	public function getFlashbags()
	{
		return $this->currentSession->getFlashbags();
	}
	
	public function getHistory()
	{
		return $this->currentSession->getHistory();
	}
	
	public function getLastHistory()
	{
		return $this->currentSession->getLastHistory();
	}
	
	public function getLifetime()
	{
		return $this->currentSession->getLifetime();
	}
	
	public function initLastUpdate()
	{
		return $this->currentSession->initLastUpdate();
	}
	
	public function initPlayerBase()
	{
		return $this->currentSession->initPlayerBase();
	}
	
	public function initPlayerBonus()
	{
		return $this->currentSession->initPlayerBonus();
	}
	
	public function initPlayerEvent()
	{
		return $this->currentSession->initPlayerEvent();
	}
	
	public function initPlayerInfo()
	{
		return $this->currentSession->initPlayerInfo();
	}
	
	public function remove($key)
	{
		return $this->currentSession->remove($key);
	}
	
	public function removeBase($key, $id)
	{
		return $this->currentSession->removeBase($key, $id);
	}
	
	public function get($key)
	{
		return $this->currentSession->get($key);
	}
}