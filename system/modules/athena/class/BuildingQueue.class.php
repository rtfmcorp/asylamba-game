<?php

/**
 * Building Queue
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 21.04.13
*/

class BuildingQueue {
	// ATTRIBUTES
	private $id 			= 0;
	private $rOrbitalBase 	= 0;
	private $buildingNumber = 0;
	private $targetLevel	= 0;
	private $remainingTime 	= 0; // en secondes
	private $position 		= 0;

	// GETTERS AND SETTERS
	public function getId() 			{ return $this->id; }
	public function getROrbitalBase() 	{ return $this->rOrbitalBase; }
	public function getBuildingNumber() { return $this->buildingNumber; }
	public function getTargetLevel() 	{ return $this->targetLevel; }
	public function getRemainingTime()	{ return $this->remainingTime; }
	public function getPosition()		{ return $this->position; }

	public function setId($v) {
		if (isset($v) && $v > 0) {
			$this->id = $v;
		} else {
			CTR::$alert->add('l\'id dans la file d\'attente est invalide');
		}
	}

	public function setROrbitalBase($v) {
		if (isset($v)) {
			$this->rOrbitalBase = $v;
		} else {
			CTR::$alert->add('l\'id de la base orbitale est invalide');
		}
	}

	public function setBuildingNumber($v) {
		if (isset($v) && OrbitalBaseResource::isABuilding($v)) {
			$this->buildingNumber = $v; 
		} else {
			CTR::$alert->add('ce vaisseau spatial n\'existe pas');
		}
	}

	public function setTargetLevel($v) {
		if (isset($v) && $v > 0 && $v <= 20) {
			$this->targetLevel = $v; 
		} else {
			CTR::$alert->add('le niveau a atteindre du bâtiment est invalide');
		}
	}

	public function setRemainingTime($v) {
		if (isset($v) && $v >= 0) {
			$this->remainingTime = $v; 
		} else {
			CTR::$alert->add('le temps restant est invalide');
		}
	}

	public function setPosition($v)	{
		if (isset($v) && $v > 0) {
			$this->position = $v; 
		} else {
			CTR::$alert->add('la position dans la file d\'attente est invalide');
		}
	}
}
?>