<?php

namespace App\Modules\Atlas\Model;

class Ranking
{
	/** @var int */
	protected $id;
	/** @var bool **/
	protected $isPlayer;
	/** @var bool **/
	protected $isFaction;
	/** @var string **/
	protected $createdAt;
	
	/**
	 * @param int $id
	 * @return Ranking
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
	 * @param bool $isPlayer
	 * @return Ranking
	 */
	public function setIsPlayer($isPlayer)
	{
		$this->isPlayer = $isPlayer;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function getIsPlayer()
	{
		return $this->isPlayer;
	}
	
	/**
	 * @param bool $isFaction
	 * @return Ranking
	 */
	public function setIsFaction($isFaction)
	{
		$this->isFaction = $isFaction;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function getIsFaction()
	{
		return $this->isFaction;
	}
	
	/**
	 * @param string $createdAt
	 * @return Ranking
	 */
	public function setCreatedAt($createdAt)
	{
		$this->createdAt = $createdAt;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
}
