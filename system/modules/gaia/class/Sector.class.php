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

class Sector {
	protected $id = 0;

	protected $rColor;
	protected $xPosition;
	protected $yPosition;
	protected $xBarycentric;
	protected $yBarycentric;
	protected $tax;

	protected $population;
	protected $lifePlanet;
	protected $ruine;
	protected $nebuleuse;
	protected $geante;
	protected $nJaune;
	protected $nRouge;

	protected $description;
	protected $systems = array();

	public function __construct() {}

	// GETTER
	public function getId() 			{ return $this->id; }
	public function getRColor()			{ return $this->rColor; }
	public function getXPosition() 		{ return $this->xPosition; }
	public function getYPosition() 		{ return $this->yPosition; }
	public function getXBarycentric() 	{ return $this->xBarycentric; }
	public function getYBarycentric() 	{ return $this->yBarycentric; }
	public function getTax() 			{ return $this->tax; }
	public function getPopulation() 	{ return $this->population; }
	public function getLifePlanet() 	{ return $this->lifePlanet; }
	public function getRuine() 			{ return $this->ruine; }
	public function getNebuleuse() 		{ return $this->nebuleuse; }
	public function getGeante()			{ return $this->geante; }
	public function getNJaune()			{ return $this->nJaune; }
	public function getNRouge()			{ return $this->nRouge; }
	public function getDescription() 	{ return $this->description; }

	// SETTER
	public function setId($v)			{ $this->id = $v; }
	public function setRColor($v)		{ $this->rColor = $v; }
	public function setXPosition($v) 	{ $this->xPosition = $v; }
	public function setYPosition($v) 	{ $this->yPosition = $v; }
	public function setXBarycentric($v) { $this->xBarycentric = $v; }
	public function setYBarycentric($v) { $this->yBarycentric = $v; }
	public function setTax($v) 			{ $this->tax = $v; }
	public function setPopulation($v) 	{ $this->population = $v; }
	public function setLifePlanet($v) 	{ $this->lifePlanet = $v; }
	public function setRuine($v) 		{ $this->ruine = $v; }
	public function setNebuleuse($v) 	{ $this->nebuleuse = $v; }
	public function setGeante($v)		{ $this->geante = $v; }
	public function setNJaune($v)		{ $this->nJaune = $v; }
	public function setNRouge($v)		{ $this->nRouge = $v; }
	public function setDescription($v) 	{ $this->description = $v; }

	public function getSystemsByPosition($i) {
		return $this->systems[$i];
	}

	public function systemsSize() {
		return count($this->systems);
	}
}