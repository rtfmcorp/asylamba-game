<?php

/**
 * ShipQueue
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 24.04.13
*/

class ShipQueue {
	// ATTRIBUTES
	private $id 			= 0;
	private $rOrbitalBase 	= 0;
	private $dockType 		= 0;
	private $shipNumber 	= 0;
	private $quantity		= 1;
	private $remainingTime 	= 0; // en secondes
	private $position 		= 0;

	// GETTERS AND SETTERS
	public function getId() 			{ return $this->id; }
	public function getROrbitalBase() 	{ return $this->rOrbitalBase; }
	public function getDockType()		{ return $this->dockType; }
	public function getShipNumber() 	{ return $this->shipNumber; }
	public function getQuantity() 		{ return $this->quantity; }
	public function getRemainingTime()	{ return $this->remainingTime; }
	public function getPosition()		{ return $this->position; }

	public function setId($v) {
		$this->id = $v;
	}

	public function setROrbitalBase($v) {
		$this->rOrbitalBase = $v;
	}

	public function setDockType($v) {
		if (isset($v) && ($v == 1 || $v == 2 || $v = 3)) {
			$this->dockType = $v; 
		} else {
			CTR::$alert->add('Le type de chantier spacial est invalide');
		}
	}

	public function setShipNumber($v) {
		if (isset($v) && ShipResource::isAShip($v)) {
			$this->shipNumber = $v; 
		} else {
			CTR::$alert->add('Ce vaisseau spacial n\'existe pas');
		}
	}

	public function setQuantity($v) {
		if (isset($v) && $v > 0) {
			$this->quantity= $v; 
		} else {
			CTR::$alert->add('Le nombre de vaisseaux est invalide');
		}
	}

	public function setRemainingTime($v) {
		if (isset($v) && $v >= 0) {
			$this->remainingTime = $v; 
		} else {
			CTR::$alert->add('Le temps restant est invalide');
		}
	}

	public function setPosition($v)	{
		if (isset($v) && $v > 0) {
			$this->position = $v; 
		} else {
			CTR::$alert->add('La position dans la file d\'attente est invalide');
		}
	}
}
?>