<?php

/**
 * loi
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 29.09.14
*/
namespace App\Modules\Demeter\Model\Law;

class Law {
	const VOTATION					= 0;
	const EFFECTIVE					= 1;
	const OBSOLETE					= 2;
	const REFUSED					= 3;

	const SECTORTAX					= 1;
	const SECTORNAME				= 2;
	const COMTAXEXPORT				= 3;
	const COMTAXIMPORT				= 4;
	const MILITARYSUBVENTION		= 5;
	const TECHNOLOGYTRANSFER		= 6;
	const PEACEPACT					= 7;
	const WARDECLARATION			= 8;
	const TOTALALLIANCE				= 9;
	const NEUTRALPACT				= 10;
	const PUNITION					= 11;


	const VOTEDURATION 				= 86400;

	public $id					= 0;
	public $rColor				= 0;
	public $type 				= '';
	public $options 			= array();
	public $statement 			= 0;
	public $dEndVotation		= '';
	public $dEnd 	 			= '';
	public $dCreation 			= '';

	public $forVote 			= 0;
	public $againstVote			= 0;

	/**
	 * @param int $id
	 * @return Law
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
	 * @param int $factionId
	 * @return Law
	 */
	public function setFactionId($factionId)
	{
		$this->rColor = $factionId;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getFactionId()
	{
		return $this->rColor;
	}
	
	/**
	 * @param int $type
	 * @return Law
	 */
	public function setType($type)
	{
		$this->type = $type;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * @param array $options
	 * @return Law
	 */
	public function setOptions($options)
	{
		$this->options = $options;
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}
	
	/**
	 * @param int $statement
	 * @return Law
	 */
	public function setStatement($statement)
	{
		$this->statement = $statement;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getStatement()
	{
		return $this->statement;
	}
	
	/**
	 * @param string $createdAt
	 * @return Law
	 */
	public function setCreatedAt($createdAt)
	{
		$this->dCreation = $createdAt;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getCreatedAt()
	{
		return $this->dCreation;
	}
	
	/**
	 * @param string $votedAt
	 * @return Law
	 */
	public function setVotedAt($votedAt)
	{
		$this->dEndVotation = $votedAt;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getVotedAt()
	{
		return $this->dEndVotation;
	}
	
	/**
	 * @param string $endedAt
	 * @return Law
	 */
	public function setEndedAt($endedAt)
	{
		$this->dEnd = $endedAt;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getEndedAt()
	{
		return $this->dEnd;
	}
	
	/**
	 * @param int $nbPositiveVotes
	 * @return Law
	 */
	public function setNbPositiveVotes($nbPositiveVotes)
	{
		$this->forVote = $nbPositiveVotes;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getNbPositiveVotes()
	{
		return $this->forVote;
	}
	
	/**
	 * @param int $nbNegativeVotes
	 * @return Law
	 */
	public function setNbNegativeVotes($nbNegativeVotes)
	{
		$this->againstVote = $nbNegativeVotes;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getNbNegativeVotes()
	{
		return $this->againstVote;
	}
}
