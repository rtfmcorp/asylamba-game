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

	// SETTER
	public function setId($v)			{ $this->id = $v; }
	public function setRColor($v)		{ $this->rColor = $v; }
	public function setXPosition($v) 	{ $this->xPosition = $v; }
	public function setYPosition($v) 	{ $this->yPosition = $v; }
	public function setXBarycentric($v) { $this->xBarycentric = $v; }
	public function setYBarycentric($v) { $this->yBarycentric = $v; }
	public function setTax($v) 			{ $this->tax = $v; }
	public function setName($v) 		{ $this->name = $v; }
	public function setPoints($v) 		{ $this->points = $v; }
	public function setPopulation($v) 	{ $this->population = $v; }
	public function setLifePlanet($v) 	{ $this->lifePlanet = $v; }

	public function getSystemsByPosition($i) {
		return $this->systems[$i];
	}

	public function systemsSize() {
		return count($this->systems);
	}
}