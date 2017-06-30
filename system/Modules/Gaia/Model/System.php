<?php

/**
 * System
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update xx.xx.xx
*/
namespace Asylamba\Modules\Gaia\Model;

class System {
	/** @var int **/
	public $id			 	= 0;
	/** @var int **/
	public $rSector 		= 0;
	/** @var int **/
	public $rColor			= 0;
	/** @var int **/
	public $xPosition		= 0;
	/** @var int **/
	public $yPosition		= 0;
	/** @var int **/
	public $typeOfSystem	= 0;

	/**
	 * @param int $id
	 * @return System
	 */
	public function setId($id)
	{
		$this->id = $id;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param int $sectorId
	 * @return System
	 */
	public function setSectorId($sectorId)
	{
		$this->rSector = $sectorId;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getSectorId()
	{
		return $this->rSector;
	}
	
	/**
	 * @param int $factionId
	 * @return System
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
	 * @param int $xPosition
	 * @return System
	 */
	public function setXPosition($xPosition)
	{
		$this->xPosition = $xPosition;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getXPosition()
	{
		return $this->xPosition;
	}
	
	/**
	 * @param int $yPosition
	 * @return System
	 */
	public function setYPosition($yPosition)
	{
		$this->yPosition = $yPosition;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getYPosition()
	{
		return $this->yPosition;
	}
	
	/**
	 * @param int $systemType
	 * @return System
	 */
	public function setSystemType($systemType)
	{
		$this->typeOfSystem = $systemType;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getSystemType()
	{
		return $this->typeOfSystem;
	}
}