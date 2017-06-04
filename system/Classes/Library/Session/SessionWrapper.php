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
	
	public function clearWrapper()
	{
		$this->currentSession = null;
	}
	
	public function add($key, $value)
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->add($key, $value);
	}
	
	public function addBase($key, $id, $name, $sector, $system, $img, $type)
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->addBase($key, $id, $name, $sector, $system, $img, $type);
	}
	
	public function addFlashbag($message, $type)
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->addFlashbag($message, $type);
	}
	
	public function addHistory($path)
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->addHistory($path);
	}
	
	public function all()
	{
		if ($this->currentSession === null) {
			return [];
		}
		return $this->currentSession->all();
	}
	
	public function baseExist($id)
	{
		if ($this->currentSession === null) {
			return false;
		}
		return $this->currentSession->baseExist($id);
	}
	
	public function clear()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->clear();
	}
	
	public function destroy()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->destroy();
	}
	
	public function exist($key)
	{
		if ($this->currentSession === null) {
			return false;
		}
		return $this->currentSession->exist($key);
	}
	
	public function flushFlashbags()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->flushFlashbags();
	}
	
	public function getFlashbags()
	{
		if ($this->currentSession === null) {
			return [];
		}
		return $this->currentSession->getFlashbags();
	}
	
	public function getHistory()
	{
		if ($this->currentSession === null) {
			return [];
		}
		return $this->currentSession->getHistory();
	}
	
	public function getLastHistory()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->getLastHistory();
	}
	
	public function getLifetime()
	{
		if ($this->currentSession === null) {
			return 0;
		}
		return $this->currentSession->getLifetime();
	}
	
	public function initLastUpdate()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->initLastUpdate();
	}
	
	public function initPlayerBase()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->initPlayerBase();
	}
	
	public function initPlayerBonus()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->initPlayerBonus();
	}
	
	public function initPlayerEvent()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->initPlayerEvent();
	}
	
	public function initPlayerInfo()
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->initPlayerInfo();
	}
	
	public function remove($key)
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->remove($key);
	}
	
	public function removeBase($key, $id)
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->removeBase($key, $id);
	}
	
	public function get($key)
	{
		if ($this->currentSession === null) {
			return null;
		}
		return $this->currentSession->get($key);
	}
}