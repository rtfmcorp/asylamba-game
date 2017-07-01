<?php

/**
 * Sector
 *
 * @author Expansion
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update xx.xx.xx
*/
namespace Asylamba\Modules\Gaia\Model;

class Sector {
	public $id = 0;

	public $rColor;
	public $rSurrender;
	public $xPosition;
	public $yPosition;
	public $xBarycentric;
	public $yBarycentric;
	public $tax;
	public $name;
	public $points;

	public $population;
	public $lifePlanet;
	/** @var boolean **/
	public $prime;

	# public $prime

	public $systems = array();

	public function __construct() {}

	// GETTER
	public function getId() 			{ return $this->id; }
	public function getRColor()			{ return $this->rColor; }
	public function getXPosition() 		{ return $this->xPosition; }
	public function getYPosition() 		{ return $this->yPosition; }
	public function getXBarycentric() 	{ return $this->xBarycentric; }
	public function getYBarycentric() 	{ return $this->yBarycentric; }
	public function getTax() 			{ return $this->tax; }
	public function getName() 			{ return $this->name; }
	public function getPoints() 		{ return $this->points; }
	public function getPopulation() 	{ return $this->population; }
	public function getLifePlanet() 	{ return $this->lifePlanet; }
	
	/**
	 * @return boolean
	 */
	public function getPrime()
	{
		return $this->prime;
	}

	// SETTER
	public function setId($v)			{ $this->id = $v; return $this; }
	public function setRColor($v)		{ $this->rColor = $v; return $this; }
	public function setXPosition($v) 	{ $this->xPosition = $v; return $this; }
	public function setYPosition($v) 	{ $this->yPosition = $v; return $this; }
	public function setXBarycentric($v) { $this->xBarycentric = $v; return $this; }
	public function setYBarycentric($v) { $this->yBarycentric = $v; return $this; }
	public function setTax($v) 			{ $this->tax = $v; return $this; }
	public function setName($v) 		{ $this->name = $v; return $this; }
	public function setPoints($v) 		{ $this->points = $v; return $this; }
	public function setPopulation($v) 	{ $this->population = $v; return $this; }
	public function setLifePlanet($v) 	{ $this->lifePlanet = $v; return $this; }
	
	/**
	 * @param boolean $isPrime
	 * @return \Asylamba\Modules\Gaia\Model\Sector
	 */
	public function setPrime($isPrime)
	{
		$this->prime = $isPrime;
		
		return $this;
	}

	public function getSystemsByPosition($i) {
		return $this->systems[$i];
	}

	public function systemsSize() {
		return count($this->systems);
	}
}